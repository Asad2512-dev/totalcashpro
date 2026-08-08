<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;
use App\Enums\BillStatus;
use App\Enums\FinanceStatus;
use App\Enums\SupplierInvoiceStatus;
use App\Enums\WageStatus;
use App\Models\CashUp;
use App\Models\User;
use App\Repositories\Contracts\BillRepositoryInterface;
use App\Repositories\Contracts\FinanceIncomeRepositoryInterface;
use App\Repositories\Contracts\FinanceSupplierPaymentRepositoryInterface;
use App\Repositories\Contracts\SpendingRepositoryInterface;
use App\Repositories\Contracts\WageRepositoryInterface;
use App\Services\BusinessAdmin\BranchContext;
use Illuminate\Support\Carbon;

final class FinanceDashboardService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly FinanceIncomeRepositoryInterface $income,
        private readonly SpendingRepositoryInterface $expenses,
        private readonly BillRepositoryInterface $bills,
        private readonly WageRepositoryInterface $wages,
        private readonly FinanceSupplierPaymentRepositoryInterface $supplierPayments,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function snapshot(User $user, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);
        $from ??= now()->startOfMonth();
        $to ??= now()->endOfMonth();
        $fromStr = $from->toDateString();
        $toStr = $to->toDateString();

        $this->bills->markOverdue($orgId);

        $cashUpRevenue = (float) CashUp::query()
            ->where('organization_id', $orgId)
            ->whereDate('cashup_date', '>=', $fromStr)
            ->whereDate('cashup_date', '<=', $toStr)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get()
            ->sum(fn (CashUp $c) => $c->revenueTotal());

        $manualIncome = $this->income->sumManualForPeriod($orgId, $branchId, $fromStr, $toStr);
        $expenseTotal = $this->expenses->sumForPeriod($orgId, $branchId, $fromStr, $toStr);
        $billsDue = $this->bills->dueTotal($orgId, $branchId);

        $payrollDue = (float) $this->wages->totalPendingAmount($orgId, $branchId);
        $supplierPaid = $this->supplierPayments->sumForPeriod($orgId, $branchId, $fromStr, $toStr);

        $payrollPaid = (float) \App\Models\Wage::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', WageStatus::Paid->value)
            ->whereBetween('paid_date', [$fromStr, $toStr])
            ->sum('gross_amount');

        $totalIncome = $cashUpRevenue + $manualIncome;
        $totalOut = $expenseTotal + $supplierPaid + $payrollPaid;

        return [
            'period' => ['from' => $from, 'to' => $to],
            'income' => $totalIncome,
            'cash_up_income' => $cashUpRevenue,
            'manual_income' => $manualIncome,
            'expenses' => $expenseTotal,
            'bills_due' => $billsDue,
            'payroll_due' => $payrollDue,
            'supplier_payments' => $supplierPaid,
            'net_position' => $totalIncome - $totalOut,
            'counts' => [
                'draft_expenses' => $this->countByStatus($orgId, $branchId, FinanceStatus::Draft),
                'approved_bills' => $this->countBills($orgId, $branchId, [BillStatus::Approved, BillStatus::Pending, BillStatus::Overdue]),
                'pending_payroll' => $this->wages->getPendingWages($orgId, $branchId)->count(),
            ],
        ];
    }

    private function countByStatus(int $orgId, ?int $branchId, FinanceStatus $status): int
    {
        return (int) \App\Models\Spending::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', $status->value)
            ->count();
    }

    /**
     * @param  list<BillStatus>  $statuses
     */
    private function countBills(int $orgId, ?int $branchId, array $statuses): int
    {
        return (int) \App\Models\Bill::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', array_map(fn (BillStatus $s) => $s->value, $statuses))
            ->count();
    }
}
