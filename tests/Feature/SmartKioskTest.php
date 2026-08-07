<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\KioskActivityEvent;
use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\BranchKiosk;
use App\Models\KioskActivityLog;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class SmartKioskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disableCookieEncryption();
    }

    public function test_public_kiosk_url_loads(): void
    {
        ['kiosk' => $kiosk] = $this->makeKioskSetup();

        $this->get(route('kiosk.show', $kiosk->token))
            ->assertOk()
            ->assertSee('Smart Attendance Kiosk')
            ->assertSee('Start kiosk session')
            ->assertSee('Start your smart attendance kiosk', false);
    }

    public function test_admin_can_start_kiosk_session_and_staff_pin_clocks_in(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk] = $this->makeKioskSetup();

        $this->postJson(route('kiosk.start', $kiosk->token), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertOk();

        $this->assertDatabaseCount('kiosk_sessions', 1);
        $sessionToken = $this->latestSessionToken($kiosk);
        $this->assertNotEmpty($sessionToken);

        $this->call(
            'POST',
            route('kiosk.pin', $kiosk->token),
            ['pin' => '1000'],
            ['tcp_kiosk_session' => $sessionToken],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['pin' => '1000']),
        )
            ->assertOk()
            ->assertJsonPath('action', 'clock-in')
            ->assertJsonPath('user.name', 'Jamie Staff');

        $this->assertDatabaseHas('kiosk_activity_logs', [
            'branch_kiosk_id' => $kiosk->id,
            'event' => KioskActivityEvent::ClockIn->value,
        ]);
    }

    public function test_second_pin_clocks_out_automatically(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk] = $this->makeKioskSetup();

        $this->postJson(route('kiosk.start', $kiosk->token), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertOk();

        $cookie = $this->latestSessionToken($kiosk);

        $pin = fn () => $this->call(
            'POST',
            route('kiosk.pin', $kiosk->token),
            ['pin' => '1000'],
            ['tcp_kiosk_session' => $cookie],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['pin' => '1000']),
        );

        $pin()->assertOk()->assertJsonPath('action', 'clock-in');

        $pin()->assertOk()->assertJsonPath('action', 'clock-out');
    }

    public function test_business_admin_kiosk_management_page(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk] = $this->makeKioskSetup();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.kiosks.index'))
            ->assertOk()
            ->assertSee('Smart Kiosks')
            ->assertSee($kiosk->publicUrl());
    }

    public function test_clock_in_route_redirects_to_kiosks(): void
    {
        ['admin' => $admin] = $this->makeKioskSetup();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.clock-in'))
            ->assertRedirect('/business-admin/kiosks');
    }

    public function test_empty_kiosk_list_shows_create_for_all_branches(): void
    {
        ['admin' => $admin] = $this->makeKioskSetup();

        BranchKiosk::query()->delete();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.kiosks.index'))
            ->assertOk()
            ->assertSee('Create Your First Kiosk')
            ->assertSee('Create Kiosk', false);
    }

    public function test_admin_can_close_kiosk_with_password_only(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk] = $this->makeKioskSetup();

        $this->postJson(route('kiosk.start', $kiosk->token), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertOk();

        $sessionToken = $this->latestSessionToken($kiosk);

        $this->call(
            'POST',
            route('kiosk.exit', $kiosk->token),
            [],
            ['tcp_kiosk_session' => $sessionToken],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['password' => 'password']),
        )
            ->assertOk()
            ->assertJsonPath('message', 'Kiosk closed.');

        $this->assertDatabaseHas('kiosk_activity_logs', [
            'branch_kiosk_id' => $kiosk->id,
            'event' => KioskActivityEvent::KioskClosed->value,
        ]);
    }

    public function test_can_create_multiple_kiosks_for_same_branch(): void
    {
        ['admin' => $admin, 'kiosk' => $existing] = $this->makeKioskSetup();

        $this->actingAsVerified($admin)
            ->post(route('business-admin.kiosks.store'), [
                'branch_id' => $existing->branch_id,
                'name' => 'Back Office Kiosk',
            ])
            ->assertRedirect(route('business-admin.kiosks.index'));

        $this->assertDatabaseCount('branch_kiosks', 2);
        $this->assertDatabaseHas('branch_kiosks', [
            'branch_id' => $existing->branch_id,
            'name' => 'Back Office Kiosk',
        ]);
    }

    public function test_home_page_shows_smart_kiosk_section(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Smart Attendance Kiosk');
    }

    /**
     * @return array{admin: User, kiosk: BranchKiosk}
     */
    private function makeKioskSetup(): array
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();
        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();

        $org = Organization::query()->create([
            'name' => 'Smart Kiosk Org',
            'slug' => 'smart-kiosk-org',
            'email' => 'kiosk@org.test',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $branch = Branch::query()->create([
            'organization_id' => $org->id,
            'name' => 'Main',
            'slug' => 'main',
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
            'name' => 'Kiosk Admin',
            'email' => 'admin-kiosk@test.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'organization_id' => $org->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $org->update(['owner_user_id' => $admin->id]);

        User::query()->create([
            'name' => 'Jamie Staff',
            'email' => 'jamie-smart@test.test',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'pin_code' => '1000',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $kiosk = BranchKiosk::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'name' => 'Main Kiosk',
            'token' => str_repeat('a', 64),
            'welcome_message' => 'Welcome',
            'is_enabled' => true,
        ]);

        return compact('admin', 'kiosk');
    }

    private function latestSessionToken(BranchKiosk $kiosk): string
    {
        return (string) $kiosk->sessions()->whereNull('ended_at')->latest('id')->value('session_token');
    }
}
