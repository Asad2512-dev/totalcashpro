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

final class FinanceModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_accounting_and_payroll_redirect_to_finance(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.accounting'))
            ->assertRedirect(route('business-admin.finance.dashboard'));

        $this->actingAsVerified($admin)
            ->get(route('business-admin.payroll'))
            ->assertRedirect(route('business-admin.finance.payroll'));
    }

    public function test_finance_income_excludes_duplicate_cash_up_entries(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->first();
        $orgId = (int) $admin->organization_id;

        \App\Models\CashUp::query()->create([
            'organization_id' => $orgId,
            'branch_id' => $branch->id,
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'opening_float' => 0,
            'cash_sales_total' => 350,
            'coins_total' => 100,
            'notes_total' => 200,
            'cards_total' => 300,
            'expenses_total' => 50,
            'online_orders_total' => 0,
            'platform_deductions_total' => 0,
            'created_by' => $admin->id,
        ]);

        \App\Models\FinanceIncomeEntry::query()->create([
            'organization_id' => $orgId,
            'branch_id' => $branch->id,
            'title' => 'Cash up mirror',
            'source' => \App\Enums\FinanceIncomeSource::CashUp->value,
            'gross_amount' => 550,
            'net_amount' => 550,
            'vat_amount' => 0,
            'income_date' => now()->toDateString(),
            'status' => \App\Enums\FinanceStatus::Paid->value,
            'created_by' => $admin->id,
        ]);

        \App\Models\FinanceIncomeEntry::query()->create([
            'organization_id' => $orgId,
            'branch_id' => $branch->id,
            'title' => 'Catering invoice',
            'source' => \App\Enums\FinanceIncomeSource::Manual->value,
            'gross_amount' => 120,
            'net_amount' => 100,
            'vat_amount' => 20,
            'income_date' => now()->toDateString(),
            'status' => \App\Enums\FinanceStatus::Paid->value,
            'created_by' => $admin->id,
        ]);

        $snapshot = app(\App\Services\BusinessAdmin\Finance\FinanceDashboardService::class)
            ->snapshot($admin, now()->startOfDay(), now()->endOfDay());

        $this->assertSame(650.0, (float) $snapshot['cash_up_income']);
        $this->assertSame(120.0, (float) $snapshot['manual_income']);
        $this->assertSame(770.0, (float) $snapshot['income']);
    }

    public function test_payroll_can_be_generated_from_attendance(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->first();

        User::query()->create([
            'name' => 'Finance Staff',
            'email' => 'finance-staff@example.com',
            'password' => Hash::make('password'),
            'role_id' => Role::query()->where('slug', RoleSlug::Staff->value)->value('id'),
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'pin_hash' => \App\Support\Security\StaffPinHasher::hash('2000'),
            'hourly_rate' => 15,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAsVerified($admin)
            ->post(route('business-admin.finance.payroll.generate'), [
                'week_start' => now()->startOfWeek()->toDateString(),
                'payment_due_date' => now()->startOfWeek()->addWeek()->toDateString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('finance_payroll_runs', [
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
        ]);
    }

    private function makeBusinessAdmin(): User
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $role = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();

        $organization = Organization::query()->create([
            'name' => 'Finance Test Biz',
            'slug' => 'finance-test-biz',
            'email' => 'finance-biz@example.com',
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
            'name' => 'Finance Owner',
            'email' => 'finance-owner@example.com',
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
