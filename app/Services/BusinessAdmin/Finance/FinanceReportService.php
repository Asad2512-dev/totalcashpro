<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;
use App\Enums\FinanceStatus;
use App\Enums\WageStatus;
use App\Models\Bill;
use App\Models\CashUp;
use App\Models\FinanceIncomeEntry;
use App\Models\Spending;
use App\Models\User;
use App\Models\Wage;
use App\Repositories\Contracts\FinanceIncomeRepositoryInterface;
use App\Repositories\Contracts\FinanceSupplierPaymentRepositoryInterface;
use App\Repositories\Contracts\SpendingRepositoryInterface;
use App\Services\BusinessAdmin\BranchContext;
use Illuminate\Support\Carbon;

final class FinanceReportService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly FinanceIncomeRepositoryInterface $income,
        private readonly SpendingRepositoryInterface $expenses,
        private readonly FinanceSupplierPaymentRepositoryInterface $supplierPayments,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function cashFlow(User $user, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);
        $from ??= now()->startOfMonth();
        $to ??= now()->endOfMonth();
        $fromStr = $from->toDateString();
        $toStr = $to->toDateString();

        $inflows = $this->income->sumManualForPeriod($orgId, $branchId, $fromStr, $toStr, FinanceStatus::Paid->value);
        $inflows += (float) CashUp::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereDate('cashup_date', '>=', $fromStr)
            ->whereDate('cashup_date', '<=', $toStr)
            ->get()
            ->sum(fn (CashUp $c) => $c->netTotal());

        $expenseOut = $this->expenses->sumForPeriod($orgId, $branchId, $fromStr, $toStr, FinanceStatus::Paid->value);
        $supplierOut = $this->supplierPayments->sumForPeriod($orgId, $branchId, $fromStr, $toStr);
        $payrollOut = (float) Wage::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', WageStatus::Paid->value)
            ->whereBetween('paid_date', [$fromStr, $toStr])
            ->sum('gross_amount');

        $outflows = $expenseOut + $supplierOut + $payrollOut;

        return [
            'period' => compact('from', 'to'),
            'inflows' => $inflows,
            'outflows' => $outflows,
            'net' => $inflows - $outflows,
            'breakdown' => [
                'expenses' => $expenseOut,
                'supplier_payments' => $supplierOut,
                'payroll' => $payrollOut,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function profitAndLoss(User $user, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);
        $from ??= now()->startOfMonth();
        $to ??= now()->endOfMonth();
        $fromStr = $from->toDateString();
        $toStr = $to->toDateString();

        $revenueNet = $this->income->sumManualForPeriod($orgId, $branchId, $fromStr, $toStr);
        $revenueNet += (float) CashUp::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereDate('cashup_date', '>=', $fromStr)
            ->whereDate('cashup_date', '<=', $toStr)
            ->get()
            ->sum(fn (CashUp $c) => $c->netTotal());

        $cogs = $this->expenses->sumForPeriod($orgId, $branchId, $fromStr, $toStr);
        $operating = (float) Bill::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', 'paid')
            ->whereBetween('paid_date', [$fromStr, $toStr])
            ->sum('net_amount');

        $payroll = (float) Wage::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', WageStatus::Paid->value)
            ->whereBetween('paid_date', [$fromStr, $toStr])
            ->sum('net_amount');

        $totalCosts = $cogs + $operating + $payroll;

        return [
            'period' => compact('from', 'to'),
            'revenue_net' => $revenueNet,
            'cost_of_sales' => $cogs,
            'operating_expenses' => $operating,
            'payroll' => $payroll,
            'total_costs' => $totalCosts,
            'profit' => $revenueNet - $totalCosts,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function vatSummary(User $user, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);
        $from ??= now()->startOfQuarter();
        $to ??= now()->endOfQuarter();
        $fromStr = $from->toDateString();
        $toStr = $to->toDateString();

        $outputVat = (float) FinanceIncomeEntry::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('income_date', [$fromStr, $toStr])
            ->whereIn('status', [FinanceStatus::Approved->value, FinanceStatus::Paid->value])
            ->sum('vat_amount');

        $inputVat = (float) Spending::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('spent_date', [$fromStr, $toStr])
            ->whereIn('status', [FinanceStatus::Approved->value, FinanceStatus::Paid->value])
            ->sum('vat_amount');

        $inputVat += (float) Bill::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', 'paid')
            ->whereBetween('paid_date', [$fromStr, $toStr])
            ->sum('vat_amount');

        return [
            'period' => compact('from', 'to'),
            'output_vat' => $outputVat,
            'input_vat' => $inputVat,
            'vat_due' => $outputVat - $inputVat,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function reportsIndex(User $user): array
    {
        return [
            'cash_flow' => $this->cashFlow($user),
            'profit_loss' => $this->profitAndLoss($user),
            'vat' => $this->vatSummary($user),
        ];
    }
}
