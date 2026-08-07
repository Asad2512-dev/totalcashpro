<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\CashDrawer;
use App\Models\Organization;
use App\Models\PettyCashAccount;
use App\Models\Plan;
use App\Models\RecurringBill;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class FinanceToolsTest extends TestCase
{
    use RefreshDatabase;

    public function test_recurring_bills_page_and_store(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.finance.recurring-bills'))
            ->assertOk()
            ->assertSee('Recurring bills');

        $this->actingAsVerified($admin)
            ->post(route('business-admin.finance.recurring-bills.store'), [
                'title' => 'Monthly rent',
                'vendor' => 'Landlord Ltd',
                'amount' => 1500,
                'frequency' => 'monthly',
                'next_due_date' => now()->addMonth()->toDateString(),
            ])
            ->assertRedirect();

        $this->assertSame(1, RecurringBill::query()->where('organization_id', $admin->organization_id)->count());
    }

    public function test_petty_cash_account_and_transaction(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.finance.petty-cash'))
            ->assertOk();

        $this->actingAsVerified($admin)
            ->post(route('business-admin.finance.petty-cash.store'), [
                'name' => 'Front desk float',
                'opening_balance' => 100,
            ])
            ->assertRedirect();

        $account = PettyCashAccount::query()->firstOrFail();

        $this->actingAsVerified($admin)
            ->post(route('business-admin.finance.petty-cash.transactions.store', $account), [
                'type' => 'expense',
                'amount' => 15,
                'description' => 'Milk',
            ])
            ->assertRedirect();

        $this->assertSame(85.0, (float) $account->fresh()->balance);
    }

    public function test_cash_drawer_management(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();

        $drawer = CashDrawer::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Main Drawer',
            'opening_balance' => 100,
            'current_balance' => 100,
        ]);

        $this->actingAsVerified($admin)
            ->get(route('business-admin.finance.cash-drawers'))
            ->assertOk()
            ->assertSee('Main Drawer');

        $this->actingAsVerified($admin)
            ->put(route('business-admin.finance.cash-drawers.update', $drawer), [
                'opening_balance' => 300,
            ])
            ->assertRedirect();

        $this->assertSame(300.0, (float) $drawer->fresh()->current_balance);
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
            'email' => 'owner@example.com',
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
            'branch_id' => $organization->branches()->first()?->id,
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
