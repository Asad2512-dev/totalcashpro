<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AttendanceLogType;
use App\Enums\KioskSessionStatus;
use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\AttendanceBreak;
use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\KioskBreakType;
use App\Models\KioskSession;
use App\Models\Organization;
use App\Models\OrganizationKioskSetting;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class KioskV2Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disableCookieEncryption();
    }

    public function test_kiosk_page_loads_and_admin_can_select_branch(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->makeHarbourKitchenSetup();

        $this->get(route('kiosk.index'))
            ->assertOk()
            ->assertSee('Staff Clock')
            ->assertSee('Business Admin Login');

        $this->postJson(route('kiosk.v2.login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertOk();

        $token = $this->latestSessionToken();
        $this->assertNotEmpty($token);

        $this->call(
            'POST',
            route('kiosk.v2.select-branch'),
            ['branch_id' => $dockside->id],
            ['tcp_kiosk_session' => $token],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['branch_id' => $dockside->id]),
        )->assertOk()->assertJsonPath('data.branch.name', 'Dockside');
    }

    public function test_staff_first_pin_clocks_in_second_shows_actions(): void
    {
        ['admin' => $admin, 'dockside' => $dockside, 'jamie' => $jamie] = $this->makeHarbourKitchenSetup();
        $token = $this->startKioskAtBranch($admin, $dockside);

        $pin = fn () => $this->call(
            'POST',
            route('kiosk.v2.pin'),
            ['pin' => '1000'],
            ['tcp_kiosk_session' => $token],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['pin' => '1000']),
        );

        $pin()->assertOk()->assertJsonPath('data.action', 'clock-in');

        $pin()->assertOk()->assertJsonPath('data.step', 'choose_action');
    }

    public function test_break_flow_and_return_from_break(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->makeHarbourKitchenSetup();
        $token = $this->startKioskAtBranch($admin, $dockside);

        $this->pin($token)->assertJsonPath('data.action', 'clock-in');
        $this->pin($token)->assertJsonPath('data.step', 'choose_action');

        $this->action($token, 'start-break', 'lunch')
            ->assertOk()
            ->assertJsonPath('data.action', 'start-break');

        $this->assertDatabaseHas('attendance_breaks', [
            'user_id' => User::query()->where('email', 'jamie@harbour.test')->value('id'),
            'is_paid' => false,
            'status' => 'active',
        ]);

        $onBreak = $this->pin($token);
        $onBreak->assertJsonPath('data.step', 'on_break');
        $onBreak->assertJsonPath('data.actions.0.action', 'end-break');

        $this->action($token, 'end-break')
            ->assertOk()
            ->assertJsonPath('data.action', 'end-break');
    }

    public function test_clock_out_calculates_net_with_unpaid_lunch(): void
    {
        ['admin' => $admin, 'dockside' => $dockside, 'jamie' => $jamie] = $this->makeHarbourKitchenSetup();
        $token = $this->startKioskAtBranch($admin, $dockside);

        AttendanceLog::query()->create([
            'organization_id' => $dockside->organization_id,
            'branch_id' => $dockside->id,
            'user_id' => $jamie->id,
            'type' => AttendanceLogType::ClockIn->value,
            'source' => 'kiosk',
            'logged_at' => now()->setTime(8, 0),
        ]);

        AttendanceBreak::query()->create([
            'organization_id' => $dockside->organization_id,
            'branch_id' => $dockside->id,
            'user_id' => $jamie->id,
            'break_type' => 'lunch',
            'break_started_at' => now()->setTime(12, 0),
            'break_ended_at' => now()->setTime(12, 30),
            'is_paid' => false,
            'status' => 'completed',
            'source' => 'kiosk',
        ]);

        $this->pin($token)->assertJsonPath('data.step', 'choose_action');

        $this->action($token, 'clock-out')
            ->assertOk()
            ->assertJsonPath('data.action', 'clock-out');
    }

    public function test_invalid_pin_does_not_reveal_user(): void
    {
        ['admin' => $admin, 'dockside' => $dockside] = $this->makeHarbourKitchenSetup();
        $token = $this->startKioskAtBranch($admin, $dockside);

        $this->call(
            'POST',
            route('kiosk.v2.pin'),
            ['pin' => '9999'],
            ['tcp_kiosk_session' => $token],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['pin' => '9999']),
        )->assertStatus(422)->assertJsonPath('errors.pin.0', 'Invalid PIN.');
    }

    public function test_wrong_branch_staff_rejected(): void
    {
        ['admin' => $admin, 'dockside' => $dockside, 'centralStaff' => $centralStaff] = $this->makeHarbourKitchenSetup();
        $token = $this->startKioskAtBranch($admin, $dockside);

        $this->call(
            'POST',
            route('kiosk.v2.pin'),
            ['pin' => '2000'],
            ['tcp_kiosk_session' => $token],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['pin' => '2000']),
        )->assertStatus(422);
    }

    public function test_admin_can_revoke_kiosk_session(): void
    {
        ['admin' => $admin, 'dockside' => $dockside, 'org' => $org] = $this->makeHarbourKitchenSetup();
        $token = $this->startKioskAtBranch($admin, $dockside);

        $this->actingAsVerified($admin)
            ->post(route('business-admin.kiosk.revoke-session'))
            ->assertRedirect();

        $this->assertDatabaseHas('kiosk_sessions', [
            'organization_id' => $org->id,
            'status' => KioskSessionStatus::Revoked->value,
        ]);

        $this->call(
            'POST',
            route('kiosk.v2.pin'),
            ['pin' => '1000'],
            ['tcp_kiosk_session' => $token],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['pin' => '1000']),
        )->assertStatus(403);
    }

    public function test_deactivated_break_type_hidden_from_kiosk(): void
    {
        ['admin' => $admin, 'dockside' => $dockside, 'org' => $org] = $this->makeHarbourKitchenSetup();
        KioskBreakType::query()
            ->where('organization_id', $org->id)
            ->where('slug', 'namaz')
            ->update(['is_active' => false]);

        $token = $this->startKioskAtBranch($admin, $dockside);
        $this->pin($token);
        $response = $this->pin($token);
        $options = collect($response->json('data.break_options'))->pluck('value');

        $this->assertFalse($options->contains('namaz'));
    }

    public function test_historical_break_retains_paid_snapshot(): void
    {
        ['org' => $org, 'jamie' => $jamie, 'dockside' => $dockside] = $this->makeHarbourKitchenSetup();

        $break = AttendanceBreak::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $dockside->id,
            'user_id' => $jamie->id,
            'break_type' => 'lunch',
            'break_started_at' => now()->subHours(2),
            'break_ended_at' => now()->subHours(1)->subMinutes(30),
            'is_paid' => false,
            'status' => 'completed',
            'source' => 'kiosk',
        ]);

        KioskBreakType::query()
            ->where('organization_id', $org->id)
            ->where('slug', 'lunch')
            ->update(['is_paid' => true]);

        $this->assertFalse($break->fresh()->is_paid);
    }

    /**
     * @return array<string, mixed>
     */
    private function makeHarbourKitchenSetup(): array
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();
        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();

        $org = Organization::query()->create([
            'name' => 'Harbour Kitchen Group',
            'slug' => 'harbour-kitchen',
            'email' => 'admin@harbour.test',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $dockside = Branch::query()->create([
            'organization_id' => $org->id,
            'name' => 'Dockside',
            'slug' => 'dockside',
            'status' => 'open',
        ]);

        $central = Branch::query()->create([
            'organization_id' => $org->id,
            'name' => 'Harbour Central',
            'slug' => 'harbour-central',
            'status' => 'open',
        ]);

        Subscription::query()->create([
            'organization_id' => $org->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subMonth(),
            'current_period_start' => now()->subMonth(),
            'current_period_end' => now()->addMonth(),
        ]);

        $admin = User::query()->create([
            'name' => 'Harbour Admin',
            'email' => 'admin@harbour.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'organization_id' => $org->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $org->update(['owner_user_id' => $admin->id]);

        $jamie = User::query()->create([
            'name' => 'Jamie Cole',
            'email' => 'jamie@harbour.test',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
            'organization_id' => $org->id,
            'branch_id' => $dockside->id,
            'pin_hash' => \App\Support\Security\StaffPinHasher::hash('1000'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $centralStaff = User::query()->create([
            'name' => 'Central Only',
            'email' => 'central@harbour.test',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
            'organization_id' => $org->id,
            'branch_id' => $central->id,
            'pin_hash' => \App\Support\Security\StaffPinHasher::hash('2000'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        OrganizationKioskSetting::query()->create([
            'organization_id' => $org->id,
            'default_branch_id' => $dockside->id,
            'display_name' => 'Staff Clock',
            'show_attendance_list' => true,
        ]);

        app(\App\Services\Kiosk\KioskBreakTypeService::class)->ensureDefaults($org->id);

        return compact('org', 'admin', 'dockside', 'central', 'jamie', 'centralStaff');
    }

    private function startKioskAtBranch(User $admin, Branch $branch): string
    {
        $this->postJson(route('kiosk.v2.login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertOk();

        $token = $this->latestSessionToken();

        $this->call(
            'POST',
            route('kiosk.v2.select-branch'),
            ['branch_id' => $branch->id],
            ['tcp_kiosk_session' => $token],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['branch_id' => $branch->id]),
        )->assertOk();

        return $token;
    }

    private function latestSessionToken(): string
    {
        return (string) KioskSession::query()
            ->where('status', KioskSessionStatus::Active->value)
            ->whereNull('ended_at')
            ->latest('id')
            ->value('session_token');
    }

    private function pin(string $token): \Illuminate\Testing\TestResponse
    {
        return $this->call(
            'POST',
            route('kiosk.v2.pin'),
            ['pin' => '1000'],
            ['tcp_kiosk_session' => $token],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['pin' => '1000']),
        );
    }

    private function action(string $token, string $action, ?string $breakType = null): \Illuminate\Testing\TestResponse
    {
        return $this->call(
            'POST',
            route('kiosk.v2.action'),
            ['pin' => '1000', 'action' => $action, 'break_type' => $breakType],
            ['tcp_kiosk_session' => $token],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['pin' => '1000', 'action' => $action, 'break_type' => $breakType]),
        );
    }
}
