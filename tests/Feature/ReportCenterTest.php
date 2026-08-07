<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\CashUp;
use App\Models\FinanceIncomeEntry;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class ReportCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_center_loads_with_filters(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.reports', [
                'date_preset' => 'last_30_days',
                'report_type' => 'overview',
            ]))
            ->assertOk()
            ->assertSee('Reports Center')
            ->assertSee('Business insights');
    }

    public function test_reports_export_csv(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.reports.export', [
                'date_preset' => 'this_month',
                'report_type' => 'profit_loss',
                'format' => 'csv',
            ]))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_finance_reports_page_loads_full_center(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.finance.reports'))
            ->assertOk()
            ->assertSee('Finance Reports')
            ->assertSee('Business insights')
            ->assertDontSee('Revenue trend');
    }

    public function test_revenue_excludes_duplicate_cash_up_income_entries(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();

        CashUp::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'coins_total' => 0,
            'notes_total' => 100,
            'cards_total' => 0,
            'expenses_total' => 0,
            'online_orders_total' => 0,
            'platform_deductions_total' => 0,
            'created_by' => $admin->id,
        ]);

        FinanceIncomeEntry::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'source' => \App\Enums\FinanceIncomeSource::CashUp,
            'title' => 'Duplicate cash up income',
            'net_amount' => 100,
            'vat_amount' => 0,
            'gross_amount' => 100,
            'income_date' => now()->toDateString(),
            'status' => \App\Enums\FinanceStatus::Paid,
            'created_by' => $admin->id,
        ]);

        FinanceIncomeEntry::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'source' => \App\Enums\FinanceIncomeSource::Manual,
            'title' => 'Manual top-up',
            'net_amount' => 50,
            'vat_amount' => 0,
            'gross_amount' => 50,
            'income_date' => now()->toDateString(),
            'status' => \App\Enums\FinanceStatus::Paid,
            'created_by' => $admin->id,
        ]);

        $query = new \App\Support\Reports\ReportCenterQuery(
            (int) $admin->organization_id,
            null,
            now()->toDateString(),
            now()->toDateString(),
        );

        $this->assertSame(150.0, $query->revenueTotal());
    }

    private function makeBusinessAdmin(): User
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\PlanSeeder::class);

        $role = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();
        $plan = Plan::query()->where('slug', 'professional')->firstOrFail();

        $organization = Organization::query()->create([
            'name' => 'Report Test Biz',
            'slug' => 'report-test-biz',
            'email' => 'report-biz@example.com',
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
            'name' => 'Report Owner',
            'email' => 'report-owner@example.com',
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
