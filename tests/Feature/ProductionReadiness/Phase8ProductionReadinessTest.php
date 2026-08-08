<?php

declare(strict_types=1);

namespace Tests\Feature\ProductionReadiness;

use App\Enums\AlertStatus;
use App\Enums\AlertType;
use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\BranchKiosk;
use App\Models\BusinessAlert;
use App\Models\CashUp;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class Phase8ProductionReadinessTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->disableCookieEncryption();
    }

    public function test_security_headers_are_present_on_web_responses(): void
    {
        $response = $this->get(route('login'));

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_cash_up_cross_organization_actions_are_forbidden(): void
    {
        $adminA = $this->makeBusinessAdmin('admin-a@phase8.test');
        $adminB = $this->makeBusinessAdmin('admin-b@phase8.test');
        $branchB = $adminB->organization->branches()->firstOrFail();

        $foreignCashUp = CashUp::query()->create([
            'organization_id' => $adminB->organization_id,
            'branch_id' => $branchB->id,
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'opening_float' => 0,
            'coins_total' => 0,
            'notes_total' => 0,
            'cards_total' => 0,
            'online_orders_total' => 0,
            'platform_deductions_total' => 0,
            'created_by' => $adminB->id,
            'status' => 'draft',
        ]);

        $this->actingAsVerified($adminA)
            ->post(route('business-admin.cash-up.submit', $foreignCashUp))
            ->assertForbidden();

        $this->actingAsVerified($adminA)
            ->get(route('business-admin.cash-up.print', $foreignCashUp))
            ->assertForbidden();
    }

    public function test_business_alert_cross_organization_resolve_is_forbidden(): void
    {
        $adminA = $this->makeBusinessAdmin('alert-a@phase8.test');
        $adminB = $this->makeBusinessAdmin('alert-b@phase8.test');

        $alert = BusinessAlert::query()->create([
            'organization_id' => $adminB->organization_id,
            'alert_type' => AlertType::LowStock->value,
            'priority' => 'high',
            'status' => AlertStatus::Open->value,
            'title' => 'Foreign alert',
            'message' => 'Should not be accessible',
        ]);

        $this->actingAsVerified($adminA)
            ->post(route('business-admin.executive.alerts.resolve', $alert))
            ->assertForbidden();
    }

    public function test_staff_cannot_access_business_admin_executive_dashboard(): void
    {
        $this->seed([\Database\Seeders\RolePermissionSeeder::class, \Database\Seeders\PlanSeeder::class]);
        $admin = $this->makeBusinessAdmin('owner@phase8.test');
        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();
        $branch = $admin->organization->branches()->firstOrFail();

        $staff = User::query()->create([
            'name' => 'Staff Member',
            'email' => 'staff@phase8.test',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAsVerified($staff)
            ->get(route('business-admin.executive.index'))
            ->assertForbidden();
    }

    public function test_kiosk_pin_attempts_are_rate_limited(): void
    {
        ['admin' => $admin, 'kiosk' => $kiosk] = $this->makeKioskSetup();

        $this->postJson(route('kiosk.start', $kiosk->token), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertOk();

        $sessionToken = $this->latestSessionToken($kiosk);
        $this->assertNotEmpty($sessionToken);

        for ($i = 0; $i < 10; $i++) {
            $this->call(
                'POST',
                route('kiosk.pin', $kiosk->token),
                ['pin' => '9999'],
                ['tcp_kiosk_session' => $sessionToken],
                [],
                ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
                json_encode(['pin' => '9999']),
            )->assertStatus(422);
        }

        $this->call(
            'POST',
            route('kiosk.pin', $kiosk->token),
            ['pin' => '9999'],
            ['tcp_kiosk_session' => $sessionToken],
            [],
            ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'],
            json_encode(['pin' => '9999']),
        )->assertStatus(422)
            ->assertJsonValidationErrors('pin');
    }

    /**
     * @return array{admin: User, kiosk: BranchKiosk}
     */
    private function makeKioskSetup(): array
    {
        $this->seed([\Database\Seeders\RolePermissionSeeder::class, \Database\Seeders\PlanSeeder::class]);

        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $staffRole = Role::query()->where('slug', RoleSlug::Staff->value)->firstOrFail();
        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();

        $organization = Organization::query()->create([
            'name' => 'Kiosk Org',
            'slug' => 'kiosk-org-'.uniqid(),
            'email' => 'kiosk@phase8.test',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $branch = Branch::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Main',
            'slug' => 'main',
            'status' => 'open',
        ]);

        $admin = User::query()->create([
            'name' => 'Kiosk Admin',
            'email' => 'kiosk-admin@phase8.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'organization_id' => $organization->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
        ]);

        Subscription::query()->create([
            'organization_id' => $organization->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Active->value,
            'starts_at' => now()->subDay(),
            'current_period_start' => now()->subDay(),
            'current_period_end' => now()->addMonth(),
        ]);

        User::query()->create([
            'name' => 'Jamie Staff',
            'email' => 'jamie@phase8.test',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
            'organization_id' => $organization->id,
            'branch_id' => $branch->id,
            'pin_hash' => \App\Support\Security\StaffPinHasher::hash('1000'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $kiosk = BranchKiosk::query()->create([
            'organization_id' => $organization->id,
            'branch_id' => $branch->id,
            'name' => 'Front Desk',
            'token' => str_repeat('b', 64),
            'welcome_message' => 'Welcome',
            'is_enabled' => true,
            'created_by' => $admin->id,
        ]);

        return ['admin' => $admin, 'kiosk' => $kiosk];
    }

    private function latestSessionToken(BranchKiosk $kiosk): string
    {
        return (string) $kiosk->sessions()->latest('started_at')->value('session_token');
    }
}
