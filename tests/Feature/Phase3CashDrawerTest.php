<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CashDrawerMovementType;
use App\Enums\CashUpStatus;
use App\Enums\FinanceIncomeSource;
use App\Models\Branch;
use App\Models\CashDrawer;
use App\Models\CashDrawerSession;
use App\Models\CashUp;
use App\Models\FinanceIncomeEntry;
use App\Services\BusinessAdmin\CashDrawerService;
use App\Services\BusinessAdmin\CashMovementService;
use App\Services\BusinessAdmin\CashReconciliationService;
use App\Services\BusinessAdmin\CashUpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestUsers;
use Tests\TestCase;

final class Phase3CashDrawerTest extends TestCase
{
    use CreatesTestUsers;
    use RefreshDatabase;

    public function test_business_admin_can_manage_cash_drawers_page(): void
    {
        $admin = $this->makeBusinessAdmin();

        $this->actingAsVerified($admin)
            ->get(route('business-admin.cash-drawers'))
            ->assertOk()
            ->assertSee('Till management');
    }

    public function test_create_drawer_assigns_branch_and_default_float(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();

        /** @var CashDrawerService $service */
        $service = app(CashDrawerService::class);
        $drawer = $service->create($admin, [
            'name' => 'Till 1',
            'code' => 'T1',
            'branch_id' => $branch->id,
            'default_opening_float' => 100,
        ]);

        $this->assertSame((int) $admin->organization_id, (int) $drawer->organization_id);
        $this->assertSame($branch->id, $drawer->branch_id);
        $this->assertEquals(100.0, $drawer->defaultOpeningFloat());
    }

    public function test_open_and_close_drawer_session(): void
    {
        $admin = $this->makeBusinessAdmin();
        $drawer = $this->makeDrawer($admin, 100);

        /** @var CashDrawerService $service */
        $service = app(CashDrawerService::class);
        $session = $service->openDrawer($admin, $drawer, 100);

        $this->assertInstanceOf(CashDrawerSession::class, $session);
        $this->assertTrue($session->isOpen());
        $this->assertEquals(100.0, (float) $session->opening_float);

        $closed = $service->closeDrawer($admin, $session, 550, 550);
        $this->assertEquals(0.0, (float) $closed->variance);
        $this->assertNotNull($closed->closed_at);
    }

    public function test_cash_movements_and_transfer_between_tills(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();
        $till1 = $this->makeDrawer($admin, 200, 'Till 1');
        $till2 = CashDrawer::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => 'Till 2',
            'opening_balance' => 100,
            'current_balance' => 100,
            'is_active' => true,
            'settings' => ['default_opening_float' => 100],
        ]);

        /** @var CashMovementService $movements */
        $movements = app(CashMovementService::class);
        $movements->record($admin, $till1, CashDrawerMovementType::Withdrawal, 50, 'Bank deposit');
        $this->assertEquals(150.0, (float) $till1->refresh()->current_balance);

        $pair = $movements->transfer($admin, $till1, $till2, 100, 'Float rebalance');
        $this->assertNotNull($pair['out']->paired_transaction_id);
        $this->assertEquals(50.0, (float) $till1->refresh()->current_balance);
        $this->assertEquals(200.0, (float) $till2->refresh()->current_balance);
    }

    public function test_mandatory_accounting_regression_opening_float_is_not_revenue(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();
        session(['business_admin.branch_id' => $branch->id]);

        /** @var CashUpService $cashUps */
        $cashUps = app(CashUpService::class);

        $cashUp = $cashUps->save($admin, [
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'opening_float' => 100,
            'coins' => [],
            'notes' => [
                ['note' => '£50', 'qty' => 11, 'is_qty' => true],
            ],
            'cards' => [],
            'expenses' => [
                ['description' => 'Milk', 'amount' => 50],
            ],
            'online' => [],
            'overwrite' => true,
        ], true);

        $this->assertEquals(500.0, (float) $cashUp->cash_sales_total);
        $this->assertEquals(100.0, $cashUp->effectiveOpeningFloat());
        $this->assertEquals(500.0, $cashUp->cashSalesTotal());
        $this->assertEquals(550.0, $cashUp->calculatedExpectedCash());
        $this->assertEquals(550.0, $cashUp->physicalCashTotal());
        $this->assertEquals(0.0, $cashUp->varianceAmount());
        $this->assertEquals(500.0, $cashUp->revenueTotal());
        $this->assertNotEquals(600.0, $cashUp->revenueTotal());

        $income = FinanceIncomeEntry::query()
            ->where('reference_type', CashUp::class)
            ->where('reference_id', $cashUp->id)
            ->first();

        $this->assertNotNull($income);
        $this->assertEquals(500.0, (float) $income->net_amount);
        $this->assertSame(FinanceIncomeSource::CashUp->value, $income->source instanceof \BackedEnum ? $income->source->value : $income->source);
    }

    public function test_reconciliation_service_formula(): void
    {
        /** @var CashReconciliationService $service */
        $service = app(CashReconciliationService::class);

        $result = $service->reconcile(
            openingFloat: 100,
            actualCash: 550,
            cashExpenses: 50,
            cashSales: 500,
        );

        $this->assertEquals(550.0, $result['expected_cash']);
        $this->assertEquals(550.0, $result['actual_cash']);
        $this->assertEquals(0.0, $result['variance']);
        $this->assertEquals(500.0, $result['cash_sales']);
    }

    public function test_cash_up_submit_approve_and_lock(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();
        session(['business_admin.branch_id' => $branch->id]);

        $cashUp = CashUp::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'opening_float' => 100,
            'cash_sales_total' => 200,
            'coins_total' => 300,
            'notes_total' => 0,
            'cards_total' => 0,
            'expenses_total' => 50,
            'online_orders_total' => 0,
            'platform_deductions_total' => 0,
            'expected_cash' => 250,
            'actual_cash' => 250,
            'variance' => 0,
            'status' => CashUpStatus::Draft->value,
            'created_by' => $admin->id,
        ]);

        /** @var CashUpService $service */
        $service = app(CashUpService::class);
        $service->submit($admin, $cashUp);
        $this->assertSame(CashUpStatus::Submitted, $cashUp->refresh()->status);

        $service->approve($admin, $cashUp->refresh());
        $cashUp->refresh();
        $this->assertTrue($cashUp->isLocked());
        $this->assertNotNull($cashUp->locked_at);
    }

    public function test_locked_cash_up_cannot_be_edited(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();
        session(['business_admin.branch_id' => $branch->id]);

        CashUp::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'cashup_date' => now()->toDateString(),
            'shift' => 'Evening',
            'opening_float' => 100,
            'coins_total' => 100,
            'notes_total' => 0,
            'cards_total' => 0,
            'expenses_total' => 0,
            'online_orders_total' => 0,
            'platform_deductions_total' => 0,
            'status' => CashUpStatus::Locked->value,
            'locked_at' => now(),
            'created_by' => $admin->id,
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        app(CashUpService::class)->save($admin, [
            'cashup_date' => now()->toDateString(),
            'shift' => 'Evening',
            'coins' => [['coin' => '£1', 'qty' => 1]],
            'notes' => [],
            'cards' => [],
            'expenses' => [],
            'online' => [],
        ]);
    }

    public function test_branch_isolation_for_drawers(): void
    {
        $admin = $this->makeBusinessAdmin();
        $otherBranch = Branch::query()->create([
            'organization_id' => $admin->organization_id,
            'name' => 'Other',
            'slug' => 'other',
            'status' => 'open',
        ]);

        $foreignDrawer = CashDrawer::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $otherBranch->id,
            'name' => 'Foreign Till',
            'opening_balance' => 100,
            'current_balance' => 100,
            'is_active' => true,
        ]);

        session(['business_admin.branch_id' => $admin->organization->branches()->first()->id]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        app(CashDrawerService::class)->findForBranch($admin, $foreignDrawer->id);
    }

    public function test_cash_history_detail_page_loads(): void
    {
        $admin = $this->makeBusinessAdmin();
        $branch = $admin->organization->branches()->firstOrFail();

        $cashUp = CashUp::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'cashup_date' => now()->toDateString(),
            'shift' => 'Morning',
            'opening_float' => 100,
            'cash_sales_total' => 250,
            'coins_total' => 350,
            'notes_total' => 0,
            'cards_total' => 0,
            'expenses_total' => 0,
            'online_orders_total' => 0,
            'platform_deductions_total' => 0,
            'expected_cash' => 350,
            'actual_cash' => 350,
            'variance' => 0,
            'created_by' => $admin->id,
        ]);

        $this->actingAsVerified($admin)
            ->get(route('business-admin.cash-history.show', $cashUp))
            ->assertOk()
            ->assertSee('Reconciliation')
            ->assertSee('£250.00');
    }

    private function makeDrawer($admin, float $float = 100, string $name = 'Till 1'): CashDrawer
    {
        $branch = $admin->organization->branches()->firstOrFail();

        return CashDrawer::query()->create([
            'organization_id' => $admin->organization_id,
            'branch_id' => $branch->id,
            'name' => $name,
            'opening_balance' => $float,
            'current_balance' => $float,
            'is_active' => true,
            'settings' => ['default_opening_float' => $float],
        ]);
    }
}
