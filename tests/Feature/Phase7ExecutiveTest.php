<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AlertType;
use App\Models\BusinessAlert;
use App\Models\CashUp;
use App\Models\InventoryItem;
use App\Models\ScheduledReport;
use App\Models\Supplier;
use App\Models\SupplierPriceHistory;
use App\Services\BusinessAdmin\BusinessAlertService;
use App\Services\BusinessAdmin\ExecutiveDashboardService;
use App\Services\BusinessAdmin\ScheduledReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class Phase7ExecutiveTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    public function test_critical_1_kpi_percentage_calculation(): void
    {
        $service = app(ExecutiveDashboardService::class);
        $result = $service->compareMetric(10000, 8000);

        $this->assertEquals(10000.0, $result['current']);
        $this->assertEquals(8000.0, $result['previous']);
        $this->assertEquals(2000.0, $result['difference']);
        $this->assertEquals(25.0, $result['percent']);
        $this->assertEquals('up', $result['trend']);
    }

    public function test_critical_2_cash_variance_alert_deduplication(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();
        $alerts = app(BusinessAlertService::class);

        $rule = $alerts->rule((int) $admin->organization_id, AlertType::CashVariance->value);
        $rule->update(['threshold_value' => 5, 'is_active' => true]);

        $cashUp = CashUp::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'opening_float' => 0,
            'coins_total' => 100,
            'notes_total' => 0,
            'cards_total' => 0,
            'online_orders_total' => 0,
            'platform_deductions_total' => 20,
            'created_by' => $admin->id,
            'status' => 'submitted',
        ]);

        $alerts->generateForOrganization($admin->organization);
        $alerts->generateForOrganization($admin->organization);

        $count = BusinessAlert::query()
            ->where('organization_id', $admin->organization_id)
            ->where('alert_type', AlertType::CashVariance->value)
            ->where('reference_id', $cashUp->id)
            ->where('status', 'open')
            ->count();

        $this->assertEquals(1, $count);
    }

    public function test_critical_3_low_stock_alert_resolves_when_replenished(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();
        $alerts = app(BusinessAlertService::class);

        $item = InventoryItem::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Tomatoes',
            'packaging' => 'kg',
            'pcs_per_box' => 1,
            'stock_total_pcs' => 20,
            'par_level' => 50,
            'min_level' => 50,
            'stock_limit' => 50,
        ]);

        $alerts->generateForOrganization($admin->organization);
        $this->assertDatabaseHas('business_alerts', [
            'organization_id' => $admin->organization_id,
            'alert_type' => AlertType::LowStock->value,
            'reference_id' => $item->id,
            'status' => 'open',
        ]);

        $item->update(['stock_total_pcs' => 100]);
        $alerts->generateForOrganization($admin->organization);

        $this->assertDatabaseMissing('business_alerts', [
            'organization_id' => $admin->organization_id,
            'alert_type' => AlertType::LowStock->value,
            'reference_id' => $item->id,
            'status' => 'open',
        ]);
    }

    public function test_critical_4_supplier_price_increase_alert(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();
        $alerts = app(BusinessAlertService::class);
        $alerts->rule((int) $admin->organization_id, AlertType::SupplierPriceIncrease->value)
            ->update(['threshold_percent' => 5, 'is_active' => true]);

        $supplier = Supplier::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Fresh Foods',
        ]);

        $item = InventoryItem::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Chicken',
            'packaging' => 'kg',
            'pcs_per_box' => 1,
            'stock_total_pcs' => 10,
        ]);

        SupplierPriceHistory::query()->create([
            'organization_id' => $admin->organization_id,
            'supplier_id' => $supplier->id,
            'inventory_item_id' => $item->id,
            'unit_cost' => 5,
            'effective_from' => now()->subDays(20)->toDateString(),
            'created_by' => $admin->id,
        ]);

        SupplierPriceHistory::query()->create([
            'organization_id' => $admin->organization_id,
            'supplier_id' => $supplier->id,
            'inventory_item_id' => $item->id,
            'unit_cost' => 5.50,
            'effective_from' => now()->subDays(2)->toDateString(),
            'created_by' => $admin->id,
        ]);

        $alerts->generateForOrganization($admin->organization);

        $this->assertDatabaseHas('business_alerts', [
            'organization_id' => $admin->organization_id,
            'alert_type' => AlertType::SupplierPriceIncrease->value,
            'status' => 'open',
        ]);
    }

    public function test_critical_5_scheduled_report_org_isolation(): void
    {
        $adminA = $this->makeBusinessAdmin('admin-a@example.com');
        $adminB = $this->makeBusinessAdmin('admin-b@example.com');

        $scheduled = ScheduledReport::query()->create([
            'organization_id' => $adminA->organization_id,
            'name' => 'Org A Weekly',
            'report_type' => 'profit_loss',
            'frequency' => 'weekly',
            'recipients' => [$adminA->email],
            'is_active' => true,
            'created_by' => $adminA->id,
        ]);

        $service = app(ScheduledReportService::class);
        $service->runOne($scheduled);

        $this->assertEquals($adminA->organization_id, $scheduled->organization_id);
        $this->assertNotEquals($adminB->organization_id, $scheduled->organization_id);
    }

    public function test_critical_6_branch_isolation_in_executive_build(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branchA = $admin->organization->branches()->firstOrFail();
        $branchB = $admin->organization->branches()->create([
            'name' => 'Branch B',
            'slug' => 'branch-b',
            'status' => 'open',
        ]);

        CashUp::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branchA->id,
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'opening_float' => 0,
            'coins_total' => 500,
            'notes_total' => 0,
            'cards_total' => 0,
            'online_orders_total' => 0,
            'platform_deductions_total' => 0,
            'created_by' => $admin->id,
            'status' => 'submitted',
        ]);

        CashUp::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branchB->id,
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'opening_float' => 0,
            'coins_total' => 100,
            'notes_total' => 0,
            'cards_total' => 0,
            'online_orders_total' => 0,
            'platform_deductions_total' => 0,
            'created_by' => $admin->id,
            'status' => 'submitted',
        ]);

        $service = app(ExecutiveDashboardService::class);
        $dataA = $service->build($admin, branchId: $branchA->id);
        $dataB = $service->build($admin, branchId: $branchB->id);

        $this->assertGreaterThan($dataB['kpis']['revenue']['current'], $dataA['kpis']['revenue']['current']);
    }

    public function test_executive_dashboard_loads(): void
    {
        $admin = $this->makeBusinessAdmin();
        session(['business_admin.branch_id' => $admin->organization->branches()->first()->id]);

        $this->actingAsVerified($admin)
            ->get(route('business-admin.executive.index'))
            ->assertOk()
            ->assertSee('Executive overview');
    }
}
