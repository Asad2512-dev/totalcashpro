<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RotaVersionStatus;
use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\RotaSection;
use App\Models\RotaShift;
use App\Models\RotaVersion;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class Phase2RotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_draft_shift(): void
    {
        ['admin' => $admin, 'section' => $section, 'staff' => $staff] = $this->makeRotaSetup();

        $this->actingAsVerified($admin)
            ->post(route('business-admin.rota.shifts.store'), [
                'user_id' => $staff->id,
                'shift_date' => now()->startOfWeek()->toDateString(),
                'start_time' => '09:00',
                'end_time' => '17:00',
                'rota_section_id' => $section->id,
                'shift_type' => 'Morning',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('rota_shifts', [
            'user_id' => $staff->id,
            'shift_type' => 'Morning',
        ]);

        $version = RotaVersion::query()->first();
        $this->assertNotNull($version);
        $this->assertSame(RotaVersionStatus::Draft, $version->status);
    }

    public function test_finalize_and_publish_workflow(): void
    {
        ['admin' => $admin, 'section' => $section, 'staff' => $staff] = $this->makeRotaSetup();

        $this->actingAsVerified($admin)->post(route('business-admin.rota.shifts.store'), [
            'user_id' => $staff->id,
            'shift_date' => now()->startOfWeek()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'rota_section_id' => $section->id,
            'shift_type' => 'Morning',
        ]);

        $draft = RotaVersion::query()->where('status', RotaVersionStatus::Draft)->firstOrFail();

        $this->actingAsVerified($admin)
            ->post(route('business-admin.rota.finalize', $draft), ['confirm' => '1'])
            ->assertRedirect();

        $draft->refresh();
        $this->assertSame(RotaVersionStatus::Finalized, $draft->status);

        $this->actingAsVerified($admin)
            ->post(route('business-admin.rota.publish', $draft), ['confirm' => '1'])
            ->assertRedirect();

        $draft->refresh();
        $this->assertSame(RotaVersionStatus::Published, $draft->status);
        $this->assertNotNull($draft->published_at);
    }

    public function test_staff_only_sees_published_rota(): void
    {
        ['admin' => $admin, 'section' => $section, 'staff' => $staff, 'branch' => $branch, 'org' => $org] = $this->makeRotaSetup();

        $week = now()->startOfWeek()->toDateString();

        $draftVersion = RotaVersion::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'week_start' => $week,
            'version_number' => 2,
            'status' => RotaVersionStatus::Draft,
        ]);

        RotaShift::query()->create([
            'rota_version_id' => $draftVersion->id,
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'user_id' => $staff->id,
            'rota_section_id' => $section->id,
            'shift_date' => $week,
            'start_time' => Carbon::parse($week.' 18:00'),
            'end_time' => Carbon::parse($week.' 22:00'),
            'shift_type' => 'Evening',
            'status' => 'draft',
        ]);

        $publishedVersion = RotaVersion::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'week_start' => $week,
            'version_number' => 1,
            'status' => RotaVersionStatus::Published,
            'published_at' => now(),
        ]);

        RotaShift::query()->create([
            'rota_version_id' => $publishedVersion->id,
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'user_id' => $staff->id,
            'rota_section_id' => $section->id,
            'shift_date' => $week,
            'start_time' => Carbon::parse($week.' 09:00'),
            'end_time' => Carbon::parse($week.' 17:00'),
            'shift_type' => 'Morning',
            'status' => 'published',
        ]);

        $response = $this->actingAsVerified($staff)->get(route('staff.shift', ['week' => $week]));

        $response->assertOk();
        $response->assertSee('09:00');
        $response->assertDontSee('18:00');
    }

    public function test_draft_does_not_affect_kiosk_enforcement(): void
    {
        ['admin' => $admin, 'section' => $section, 'staff' => $staff, 'branch' => $branch, 'org' => $org] = $this->makeRotaSetup();

        $draftVersion = RotaVersion::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'week_start' => now()->startOfWeek()->toDateString(),
            'version_number' => 1,
            'status' => RotaVersionStatus::Draft,
        ]);

        RotaShift::query()->create([
            'rota_version_id' => $draftVersion->id,
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'user_id' => $staff->id,
            'rota_section_id' => $section->id,
            'shift_date' => now()->toDateString(),
            'start_time' => now()->subHour(),
            'end_time' => now()->addHours(7),
            'shift_type' => 'Morning',
            'status' => 'draft',
        ]);

        $shift = app(\App\Services\Kiosk\RotaEnforcementService::class)->publishedShiftForToday($staff);
        $this->assertNull($shift);
    }

    public function test_published_rota_affects_kiosk_enforcement(): void
    {
        ['section' => $section, 'staff' => $staff, 'branch' => $branch, 'org' => $org] = $this->makeRotaSetup();

        $publishedVersion = RotaVersion::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'week_start' => now()->startOfWeek()->toDateString(),
            'version_number' => 1,
            'status' => RotaVersionStatus::Published,
            'published_at' => now(),
        ]);

        RotaShift::query()->create([
            'rota_version_id' => $publishedVersion->id,
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'user_id' => $staff->id,
            'rota_section_id' => $section->id,
            'shift_date' => now()->toDateString(),
            'start_time' => now()->subHour(),
            'end_time' => now()->addHours(7),
            'shift_type' => 'Morning',
            'status' => 'published',
        ]);

        $shift = app(\App\Services\Kiosk\RotaEnforcementService::class)->publishedShiftForToday($staff);
        $this->assertNotNull($shift);
    }

    public function test_conflict_detection_finds_overlap(): void
    {
        ['admin' => $admin, 'section' => $section, 'staff' => $staff, 'branch' => $branch, 'org' => $org] = $this->makeRotaSetup();

        $version = RotaVersion::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'week_start' => now()->startOfWeek()->toDateString(),
            'version_number' => 1,
            'status' => RotaVersionStatus::Draft,
        ]);

        $date = now()->startOfWeek()->toDateString();
        foreach ([['09:00', '17:00'], ['16:00', '22:00']] as [$start, $end]) {
            RotaShift::query()->create([
                'rota_version_id' => $version->id,
                'organization_id' => $org->id,
                'branch_id' => $branch->id,
                'user_id' => $staff->id,
                'rota_section_id' => $section->id,
                'shift_date' => $date,
                'start_time' => Carbon::parse($date.' '.$start),
                'end_time' => Carbon::parse($date.' '.$end),
                'shift_type' => 'Morning',
                'status' => 'draft',
            ]);
        }

        $conflicts = app(\App\Services\BusinessAdmin\RotaValidationService::class)->conflicts($version);
        $this->assertNotEmpty($conflicts);
    }

    public function test_admin_can_print_published_rota(): void
    {
        ['admin' => $admin, 'section' => $section, 'staff' => $staff, 'branch' => $branch, 'org' => $org] = $this->makeRotaSetup();

        $version = RotaVersion::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'week_start' => now()->startOfWeek()->toDateString(),
            'version_number' => 1,
            'status' => RotaVersionStatus::Published,
            'published_at' => now(),
        ]);

        RotaShift::query()->create([
            'rota_version_id' => $version->id,
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'user_id' => $staff->id,
            'rota_section_id' => $section->id,
            'shift_date' => now()->startOfWeek()->toDateString(),
            'start_time' => Carbon::parse(now()->startOfWeek()->toDateString().' 09:00'),
            'end_time' => Carbon::parse(now()->startOfWeek()->toDateString().' 17:00'),
            'shift_type' => 'Morning',
            'status' => 'published',
        ]);

        $this->actingAsVerified($admin)
            ->get(route('business-admin.rota.print', $version))
            ->assertOk()
            ->assertSee('Staff Weekly Rota')
            ->assertSee($staff->name);
    }

    public function test_staff_can_print_own_rota(): void
    {
        ['staff' => $staff, 'section' => $section, 'branch' => $branch, 'org' => $org] = $this->makeRotaSetup();

        $version = RotaVersion::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'week_start' => now()->startOfWeek()->toDateString(),
            'version_number' => 1,
            'status' => RotaVersionStatus::Published,
            'published_at' => now(),
        ]);

        RotaShift::query()->create([
            'rota_version_id' => $version->id,
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'user_id' => $staff->id,
            'rota_section_id' => $section->id,
            'shift_date' => now()->startOfWeek()->toDateString(),
            'start_time' => Carbon::parse(now()->startOfWeek()->toDateString().' 09:00'),
            'end_time' => Carbon::parse(now()->startOfWeek()->toDateString().' 17:00'),
            'shift_type' => 'Morning',
            'status' => 'published',
        ]);

        $this->actingAsVerified($staff)
            ->get(route('staff.shift.print'))
            ->assertOk()
            ->assertSee($staff->name)
            ->assertSee('09:00');
    }

    public function test_publish_notifies_staff(): void
    {
        ['admin' => $admin, 'section' => $section, 'staff' => $staff] = $this->makeRotaSetup();

        $this->actingAsVerified($admin)->post(route('business-admin.rota.shifts.store'), [
            'user_id' => $staff->id,
            'shift_date' => now()->startOfWeek()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'rota_section_id' => $section->id,
            'shift_type' => 'Morning',
        ]);

        $draft = RotaVersion::query()->firstOrFail();

        $this->actingAsVerified($admin)->post(route('business-admin.rota.publish', $draft), ['confirm' => '1']);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $staff->id,
            'type' => 'rota_published',
        ]);
    }

    public function test_cross_organization_print_is_forbidden(): void
    {
        ['admin' => $admin, 'section' => $section, 'staff' => $staff, 'branch' => $branch, 'org' => $org] = $this->makeRotaSetup();

        $version = RotaVersion::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'week_start' => now()->startOfWeek()->toDateString(),
            'version_number' => 1,
            'status' => RotaVersionStatus::Published,
        ]);

        $otherOrg = Organization::query()->create([
            'name' => 'Other Org',
            'slug' => 'other-org',
            'email' => 'other@org.test',
            'status' => 'active',
            'plan_id' => Plan::query()->first()->id,
        ]);

        $otherBranch = Branch::query()->create([
            'organization_id' => $otherOrg->id,
            'name' => 'Other Branch',
            'slug' => 'other-branch',
            'status' => 'open',
        ]);

        $otherAdmin = User::query()->create([
            'name' => 'Other Admin',
            'email' => 'other-admin@test.test',
            'password' => Hash::make('password'),
            'role_id' => Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail()->id,
            'organization_id' => $otherOrg->id,
            'branch_id' => $otherBranch->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $otherOrg->update(['owner_user_id' => $otherAdmin->id]);

        Subscription::query()->create([
            'organization_id' => $otherOrg->id,
            'plan_id' => Plan::query()->first()->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subDay(),
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);

        $response = $this->actingAsVerified($otherAdmin)
            ->get(route('business-admin.rota.print', $version));

        $this->assertContains($response->status(), [302, 403]);
        $response->assertDontSee($staff->name);
    }

    /**
     * @return array{admin: User, staff: User, org: Organization, branch: Branch, section: RotaSection}
     */
    private function makeRotaSetup(): array
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();
        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();

        $org = Organization::query()->create([
            'name' => 'Rota Org',
            'slug' => 'rota-org',
            'email' => 'rota@org.test',
            'status' => 'active',
            'plan_id' => $plan->id,
        ]);

        $branch = Branch::query()->create([
            'organization_id' => $org->id,
            'name' => 'Dockside',
            'slug' => 'dockside',
            'status' => 'open',
        ]);

        $admin = User::query()->create([
            'name' => 'Rota Admin',
            'email' => 'rota-admin@test.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $org->update(['owner_user_id' => $admin->id]);

        Subscription::query()->create([
            'organization_id' => $org->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subDay(),
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);

        $staff = User::query()->create([
            'name' => 'Rota Staff',
            'email' => 'rota-staff@test.test',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $section = RotaSection::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'name' => 'Kitchen',
            'color' => '#16A34A',
        ]);

        session(['business_admin.branch_id' => $branch->id]);

        return compact('admin', 'staff', 'org', 'branch', 'section');
    }
}
