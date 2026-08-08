<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\KioskActivityEvent;
use App\Enums\KioskSyncEventType;
use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\AttendanceBreak;
use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\BranchKiosk;
use App\Models\KioskSyncEvent;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\RotaSection;
use App\Models\RotaShift;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class Phase1SmartKioskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disableCookieEncryption();
    }

    public function test_checked_in_pin_shows_action_choices(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk] = $this->makeKioskSetup();
        $cookie = $this->startKioskSession($admin, $kiosk);

        $this->pin($kiosk, $cookie)->assertOk()->assertJsonPath('action', 'clock-in');

        $this->pin($kiosk, $cookie)
            ->assertOk()
            ->assertJsonPath('step', 'choose_action')
            ->assertJsonPath('state', 'checked_in')
            ->assertJsonFragment(['action' => 'clock-out'])
            ->assertJsonFragment(['action' => 'start-break', 'break_type' => 'lunch']);
    }

    public function test_can_start_and_end_lunch_break(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk] = $this->makeKioskSetup();
        $cookie = $this->startKioskSession($admin, $kiosk);
        $this->pin($kiosk, $cookie);

        $this->action($kiosk, $cookie, 'start-break', 'lunch')
            ->assertOk()
            ->assertJsonPath('action', 'start-break')
            ->assertJsonPath('action_label', 'Lunch Break Started');

        $this->assertDatabaseHas('attendance_breaks', [
            'user_id' => User::query()->where('email', 'jamie-smart@test.test')->value('id'),
            'break_type' => 'lunch',
            'break_ended_at' => null,
        ]);

        $choose = $this->pin($kiosk, $cookie)
            ->assertOk()
            ->assertJsonPath('step', 'choose_action')
            ->assertJsonPath('state', 'on_break');

        $this->action($kiosk, $cookie, 'end-break')
            ->assertOk()
            ->assertJsonPath('action', 'end-break');

        $this->assertNotNull(AttendanceBreak::query()->latest('id')->value('break_ended_at'));
    }

    public function test_clock_out_during_break_ends_break_and_closes_attendance(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk] = $this->makeKioskSetup();
        $cookie = $this->startKioskSession($admin, $kiosk);
        $this->pin($kiosk, $cookie);
        $this->action($kiosk, $cookie, 'start-break', 'namaz');

        $this->action($kiosk, $cookie, 'clock-out')
            ->assertOk()
            ->assertJsonPath('action', 'clock-out');

        $this->assertDatabaseHas('attendance_logs', ['type' => 'clock-out']);
        $this->assertNotNull(AttendanceBreak::query()->latest('id')->value('break_ended_at'));
    }

    public function test_strict_rota_blocks_clock_in(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk, 'staff' => $staff] = $this->makeKioskSetup();

        $kiosk->update([
            'settings' => array_merge(app(\App\Services\Kiosk\KioskConfigurationService::class)->defaults(), [
                'rota_enforcement' => 'strict',
            ]),
        ]);

        $this->createRotaShift($kiosk, $staff, 'published', now()->addHours(3), now()->addHours(11));

        $cookie = $this->startKioskSession($admin, $kiosk);

        $this->pin($kiosk, $cookie)
            ->assertOk()
            ->assertJsonPath('step', 'rota_restricted');
    }

    public function test_admin_override_allows_strict_rota_clock_in(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk, 'staff' => $staff] = $this->makeKioskSetup();

        $kiosk->update([
            'settings' => array_merge(app(\App\Services\Kiosk\KioskConfigurationService::class)->defaults(), [
                'rota_enforcement' => 'strict',
            ]),
        ]);

        $this->createRotaShift($kiosk, $staff, 'published', now()->addHours(3), now()->addHours(11));

        $cookie = $this->startKioskSession($admin, $kiosk);
        $this->pin($kiosk, $cookie)->assertJsonPath('step', 'rota_restricted');

        $this->action($kiosk, $cookie, 'clock-in-override')
            ->assertOk()
            ->assertJsonPath('action', 'clock-in');

        $this->assertDatabaseHas('attendance_logs', ['type' => 'clock-in', 'source' => 'kiosk']);
    }

    public function test_sync_endpoint_is_idempotent(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk] = $this->makeKioskSetup();
        $cookie = $this->startKioskSession($admin, $kiosk);
        $key = 'test-idempotency-key-001';

        $payload = [
            'pin' => '1000',
            'event_type' => KioskSyncEventType::ClockIn->value,
            'idempotency_key' => $key,
        ];

        $this->sync($kiosk, $cookie, $payload)->assertOk()->assertJsonPath('action', 'clock-in');
        $this->sync($kiosk, $cookie, $payload)->assertOk()->assertJsonPath('action', 'clock-in');

        $this->assertSame(1, AttendanceLog::query()->where('idempotency_key', $key)->count());
        $this->assertSame(1, KioskSyncEvent::query()->where('idempotency_key', $key)->count());
    }

    public function test_invalid_pin_does_not_reveal_details(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk] = $this->makeKioskSetup();
        $cookie = $this->startKioskSession($admin, $kiosk);

        $this->pin($kiosk, $cookie, '9999')
            ->assertStatus(422)
            ->assertJsonValidationErrors('pin');

        $this->assertDatabaseHas('kiosk_activity_logs', [
            'branch_kiosk_id' => $kiosk->id,
            'event' => KioskActivityEvent::PinFailed->value,
        ]);
    }

    public function test_disabled_kiosk_returns_forbidden(): void
    {
        ['kiosk' => $kiosk] = $this->makeKioskSetup();
        $kiosk->update(['is_enabled' => false]);

        $this->get(route('kiosk.show', $kiosk->token))->assertForbidden();
    }

    public function test_cross_branch_pin_is_rejected(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk] = $this->makeKioskSetup();

        $otherBranch = Branch::query()->create([
            'organization_id' => $kiosk->organization_id,
            'name' => 'Harbour Central',
            'slug' => 'harbour-central',
            'status' => 'open',
        ]);

        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();
        User::query()->create([
            'name' => 'Central Staff',
            'email' => 'central-staff@test.test',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
            'organization_id' => $kiosk->organization_id,
            'branch_id' => $otherBranch->id,
            'pin_hash' => \App\Support\Security\StaffPinHasher::hash('2000'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $cookie = $this->startKioskSession($admin, $kiosk);

        $this->pin($kiosk, $cookie, '2000')
            ->assertStatus(422)
            ->assertJsonValidationErrors('pin');
    }

    public function test_draft_rota_is_ignored_for_enforcement(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk, 'staff' => $staff] = $this->makeKioskSetup();

        $kiosk->update([
            'settings' => array_merge(app(\App\Services\Kiosk\KioskConfigurationService::class)->defaults(), [
                'rota_enforcement' => 'strict',
            ]),
        ]);

        $this->createRotaShift($kiosk, $staff, 'draft', now()->subHour(), now()->addHours(7));

        $cookie = $this->startKioskSession($admin, $kiosk);

        $this->pin($kiosk, $cookie)
            ->assertOk()
            ->assertJsonPath('step', 'rota_restricted');
    }

    /**
     * @return array{admin: User, kiosk: BranchKiosk, staff: User}
     */
    private function makeKioskSetup(): array
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();
        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();

        $org = Organization::query()->create([
            'name' => 'Phase1 Kiosk Org',
            'slug' => 'phase1-kiosk-org',
            'email' => 'phase1@org.test',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $branch = Branch::query()->create([
            'organization_id' => $org->id,
            'name' => 'Dockside',
            'slug' => 'dockside',
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
            'email' => 'admin-phase1@test.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'organization_id' => $org->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        $org->update(['owner_user_id' => $admin->id]);

        $staff = User::query()->create([
            'name' => 'Jamie Staff',
            'email' => 'jamie-smart@test.test',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'pin_hash' => \App\Support\Security\StaffPinHasher::hash('1000'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $kiosk = BranchKiosk::query()->create([
            'organization_id' => $org->id,
            'branch_id' => $branch->id,
            'name' => 'Front Kiosk',
            'token' => str_repeat('b', 64),
            'welcome_message' => 'Enter your 4-digit PIN',
            'is_enabled' => true,
            'settings' => app(\App\Services\Kiosk\KioskConfigurationService::class)->defaults(),
        ]);

        return compact('admin', 'kiosk', 'staff');
    }

    private function startKioskSession(User $admin, BranchKiosk $kiosk): string
    {
        $this->postJson(route('kiosk.start', $kiosk->token), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertOk();

        return $this->latestSessionToken($kiosk);
    }

    private function pin(BranchKiosk $kiosk, string $cookie, string $pin = '1000')
    {
        return $this->call(
            'POST',
            route('kiosk.pin', $kiosk->token),
            ['pin' => $pin],
            ['tcp_kiosk_session' => $cookie],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['pin' => $pin]),
        );
    }

    private function action(BranchKiosk $kiosk, string $cookie, string $action, ?string $breakType = null)
    {
        $body = ['pin' => '1000', 'action' => $action];
        if ($breakType !== null) {
            $body['break_type'] = $breakType;
        }

        return $this->call(
            'POST',
            route('kiosk.action', $kiosk->token),
            $body,
            ['tcp_kiosk_session' => $cookie],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode($body),
        );
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private function sync(BranchKiosk $kiosk, string $cookie, array $body)
    {
        return $this->call(
            'POST',
            route('kiosk.sync', $kiosk->token),
            $body,
            ['tcp_kiosk_session' => $cookie],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode($body),
        );
    }

    private function latestSessionToken(BranchKiosk $kiosk): string
    {
        return (string) $kiosk->sessions()->whereNull('ended_at')->latest('id')->value('session_token');
    }

    private function createRotaSection(BranchKiosk $kiosk): RotaSection
    {
        return RotaSection::query()->create([
            'organization_id' => $kiosk->organization_id,
            'branch_id' => $kiosk->branch_id,
            'name' => 'Floor',
        ]);
    }

    private function createRotaShift(
        BranchKiosk $kiosk,
        User $staff,
        string $versionStatus,
        \Illuminate\Support\Carbon $start,
        \Illuminate\Support\Carbon $end,
    ): RotaShift {
        $version = \App\Models\RotaVersion::query()->create([
            'organization_id' => $kiosk->organization_id,
            'branch_id' => $kiosk->branch_id,
            'week_start' => now()->startOfWeek()->toDateString(),
            'version_number' => random_int(100, 999),
            'status' => $versionStatus,
            'published_at' => $versionStatus === 'published' ? now() : null,
        ]);

        return RotaShift::query()->create([
            'rota_version_id' => $version->id,
            'organization_id' => $kiosk->organization_id,
            'branch_id' => $kiosk->branch_id,
            'user_id' => $staff->id,
            'rota_section_id' => $this->createRotaSection($kiosk)->id,
            'shift_date' => now()->toDateString(),
            'start_time' => $start,
            'end_time' => $end,
            'shift_type' => 'regular',
            'status' => $versionStatus,
        ]);
    }
}
