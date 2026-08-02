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

        $this->actingAs($admin)
            ->get(route('business-admin.dashboard'))
            ->assertOk()
            ->assertSee("Today's Cash");
    }

    public function test_cash_up_page_loads_with_auto_branch(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAs($admin)
            ->get(route('business-admin.cash-up'))
            ->assertOk()
            ->assertSee('Daily Cash Up')
            ->assertSee('Platform Deduction')
            ->assertSee('Coins Grand Total');
    }

    public function test_clock_in_and_rota_match_legacy_layout(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAs($admin)
            ->get(route('business-admin.clock-in'))
            ->assertOk()
            ->assertSee('Staff Clock-In')
            ->assertSee('security PIN');

        $this->actingAs($admin)
            ->get(route('business-admin.rota'))
            ->assertOk()
            ->assertSee('Rota Management')
            ->assertSee('Weekly Rota')
            ->assertSee('Morning Shifts')
            ->assertSee('Evening Shifts');
    }

    public function test_staff_index_is_organization_scoped(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAs($admin)
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
            'business-admin.clock-in',
            'business-admin.attendance',
            'business-admin.suppliers',
            'business-admin.payroll',
            'business-admin.rota',
            'business-admin.subscription',
            'business-admin.branches',
            'business-admin.settings',
            'business-admin.profile',
            'business-admin.notifications',
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk();
        }
    }

    public function test_clock_in_pin_verify_works(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->first();

        User::query()->create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role_id' => Role::query()->where('slug', RoleSlug::Staff->value)->value('id'),
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'pin_code' => '1000',
            'hourly_rate' => 12.5,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->postJson(route('business-admin.clock-in.verify'), ['pin' => '1000'])
            ->assertOk()
            ->assertJsonStructure(['state', 'user']);
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
