<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class BusinessAdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_business_admin_can_login_and_open_dashboard(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->post(route('login.attempt'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('business-admin.dashboard'));

        $this->actingAsVerified($admin)
            ->get(route('business-admin.dashboard'))
            ->assertOk()
            ->assertSee("Today's cash up");
    }

    public function test_cash_up_page_loads_with_auto_branch(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.cash-up'))
            ->assertOk()
            ->assertSee('Daily Cash Up')
            ->assertSee('Platform Deduction')
            ->assertSee('Coins Grand Total');
    }

    public function test_kiosks_and_rota_pages_load(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.kiosk.settings'))
            ->assertOk()
            ->assertSee('Kiosk');

        $this->actingAsVerified($admin)
            ->get(route('business-admin.rota'))
            ->assertOk()
            ->assertSee('Rota Management')
            ->assertSee('Weekly Rota')
            ->assertSee('Morning Shifts')
            ->assertSee('Evening Shifts');
    }

    public function test_business_admin_branch_filter_updates_dashboard_scope(): void
    {
        $admin = $this->makeBusinessAdmin();

        Branch::query()->create([
            'organization_id' => $admin->organization_id,
            'name' => 'Second Branch',
            'slug' => 'second-branch',
            'status' => 'open',
        ]);

        $secondBranch = Branch::query()->where('slug', 'second-branch')->firstOrFail();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.dashboard'))
            ->assertOk()
            ->assertSee('All branches')
            ->assertSee('Main')
            ->assertSee('Second Branch');

        $this->actingAsVerified($admin)
            ->post(route('business-admin.branch.select'), [
                'branch_id' => $secondBranch->id,
                'silent' => 1,
            ])
            ->assertRedirect(route('business-admin.dashboard'));

        $this->actingAsVerified($admin)
            ->get(route('business-admin.dashboard'))
            ->assertOk()
            ->assertSee('Second Branch');
    }

    public function test_staff_index_is_organization_scoped(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.staff'))
            ->assertOk();
    }

    public function test_core_business_admin_pages_load(): void
    {
        $admin = $this->makeBusinessAdmin();

        $routes = [
            'business-admin.cash-history',
            'business-admin.reports',
            'business-admin.inventory',
            'business-admin.inventory-history',
            'business-admin.kiosk.settings',
            'business-admin.attendance',
            'business-admin.suppliers',
            'business-admin.finance.dashboard',
            'business-admin.finance.income',
            'business-admin.finance.expenses',
            'business-admin.finance.bills',
            'business-admin.finance.payroll',
            'business-admin.finance.cash-flow',
            'business-admin.rota',
            'business-admin.subscription',
            'business-admin.branches',
            'business-admin.settings',
            'business-admin.profile',
            'business-admin.notifications',
        ];

        foreach ($routes as $route) {
            $this->actingAsVerified($admin)
                ->get(route($route))
                ->assertOk();
        }
    }

    public function test_legacy_clock_in_redirects_to_kiosk(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.clock-in'))
            ->assertRedirect('/kiosk');
    }

    private function makeBusinessAdmin(): User
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $role = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();

        $organization = Organization::query()->create([
            'name' => 'Test Biz',
            'slug' => 'test-biz',
            'email' => 'biz@example.com',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        Branch::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Main',
            'slug' => 'main',
            'status' => 'open',
        ]);

        $admin = User::query()->create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'organization_id' => $organization->id,
            'status' => 'active',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),
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
}
