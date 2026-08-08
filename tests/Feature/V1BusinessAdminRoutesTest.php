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
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class V1BusinessAdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('businessAdminGetRoutes')]
    public function test_business_admin_get_route_is_accessible(string $routeName): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route($routeName))
            ->assertOk();
    }

    public static function businessAdminGetRoutes(): array
    {
        return [
            ['business-admin.dashboard'],
            ['business-admin.cash-up'],
            ['business-admin.cash-history'],
            ['business-admin.staff'],
            ['business-admin.hr'],
            ['business-admin.crm'],
            ['business-admin.kiosk.settings'],
            ['business-admin.attendance'],
            ['business-admin.inventory'],
            ['business-admin.inventory-history'],
            ['business-admin.suppliers'],
            ['business-admin.purchase-orders.index'],
            ['business-admin.finance.dashboard'],
            ['business-admin.finance.income'],
            ['business-admin.finance.expenses'],
            ['business-admin.finance.bills'],
            ['business-admin.finance.recurring-bills'],
            ['business-admin.finance.petty-cash'],
            ['business-admin.finance.cash-drawers'],
            ['business-admin.finance.purchase-invoices'],
            ['business-admin.finance.supplier-payments'],
            ['business-admin.finance.bank-accounts'],
            ['business-admin.finance.cash-flow'],
            ['business-admin.finance.profit-loss'],
            ['business-admin.finance.vat'],
            ['business-admin.finance.payroll'],
            ['business-admin.finance.weekly-wages'],
            ['business-admin.rota'],
            ['business-admin.reports'],
            ['business-admin.branches'],
            ['business-admin.subscription'],
            ['business-admin.settings'],
            ['business-admin.profile'],
            ['business-admin.notifications'],
        ];
    }

    private function makeBusinessAdmin(): User
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $role = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();

        $organization = Organization::query()->create([
            'name' => 'Route Biz',
            'slug' => 'route-biz',
            'email' => 'routes@example.com',
            'country' => 'GB',
            'currency' => 'GBP',
            'timezone' => 'Europe/London',
            'status' => 'active',
        ]);

        $branch = Branch::query()->create([
            'organization_id' => $organization->id,
            'name' => 'Main',
            'slug' => 'main-routes',
            'status' => 'open',
        ]);

        $admin = User::query()->create([
            'name' => 'Owner',
            'email' => 'routes@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'organization_id' => $organization->id,
            'branch_id' => $branch->id,
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
