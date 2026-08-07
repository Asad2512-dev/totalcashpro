<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\CashDrawer;
use App\Models\CrmCustomer;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\StaffInvitationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class OnboardingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_onboarding_wizard_loads_for_new_admin(): void
    {
        $admin = $this->makeOnboardingAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.onboarding'))
            ->assertOk()
            ->assertSee('Step 1 of 8', false);
    }

    public function test_onboarding_saves_business_step(): void
    {
        $admin = $this->makeOnboardingAdmin();

        $this->actingAsVerified($admin)
            ->post(route('business-admin.onboarding.store'), [
                'step' => 2,
                'business_name' => 'Updated Cafe',
                'phone' => '+44 7700 900111',
                'tax_number' => 'GB123',
            ])
            ->assertRedirect(route('business-admin.onboarding', ['step' => 3]));

        $this->assertSame('Updated Cafe', $admin->organization->fresh()->name);
    }

    public function test_onboarding_creates_cash_drawer_and_completes(): void
    {
        Notification::fake();
        $admin = $this->makeOnboardingAdmin();

        $this->actingAsVerified($admin)
            ->post(route('business-admin.onboarding.store'), [
                'step' => 8,
                'business_name' => 'Final Cafe',
                'branch_name' => 'High Street',
                'currency' => 'GBP',
                'timezone' => 'Europe/London',
                'vat_rate' => 20,
                'drawer_opening_balance' => 250,
                'staff_invites' => 'newstaff@example.com',
                'supplier_name' => 'Fresh Foods Ltd',
            ])
            ->assertRedirect(route('business-admin.dashboard'));

        $admin->refresh();
        $this->assertNotNull($admin->onboarding_completed_at);

        $branch = Branch::query()->where('organization_id', $admin->organization_id)->first();
        $this->assertNotNull($branch);
        $this->assertSame('High Street', $branch->name);

        $drawer = CashDrawer::query()->where('branch_id', $branch->id)->first();
        $this->assertNotNull($drawer);
        $this->assertSame(250.0, (float) $drawer->opening_balance);

        $staff = User::query()->where('email', 'newstaff@example.com')->first();
        $this->assertNotNull($staff);
        Notification::assertSentTo($staff, StaffInvitationNotification::class);
    }

    public function test_completed_onboarding_redirects_to_dashboard(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.onboarding'))
            ->assertRedirect(route('business-admin.dashboard'));
    }

    public function test_onboarding_can_be_skipped(): void
    {
        $admin = $this->makeOnboardingAdmin();

        $this->actingAsVerified($admin)
            ->post(route('business-admin.onboarding.skip'))
            ->assertRedirect(route('business-admin.dashboard'));

        $this->assertNotNull($admin->fresh()->onboarding_completed_at);
    }

    private function makeOnboardingAdmin(): User
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $role = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();

        $organization = Organization::query()->create([
            'name' => 'Onboarding Biz',
            'slug' => 'onboarding-biz',
            'email' => 'onboard@example.com',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        Branch::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Main Branch',
            'slug' => 'main-branch',
            'status' => 'open',
        ]);

        $admin = User::query()->create([
            'name' => 'Owner',
            'email' => 'onboard@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'organization_id' => $organization->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => null,
        ]);

        $organization->update(['owner_user_id' => $admin->id]);

        Subscription::query()->create([
            'organization_id' => $organization->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subDay(),
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);

        return $admin->fresh();
    }

    private function makeBusinessAdmin(): User
    {
        $admin = $this->makeOnboardingAdmin();
        $admin->update(['onboarding_completed_at' => now()]);

        return $admin->fresh();
    }
}
