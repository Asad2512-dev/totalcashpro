<?php

declare(strict_types=1);

namespace App\Support\Reports;

use App\Enums\AttendanceLogType;
use App\Enums\BillStatus;
use App\Enums\FinanceIncomeSource;
use App\Enums\FinanceStatus;
use App\Enums\SupplierInvoiceStatus;
use App\Enums\WageStatus;
use App\Models\AttendanceLog;
use App\Models\Bill;
use App\Models\CashUp;
use App\Models\FinanceIncomeEntry;
use App\Models\FinanceSupplierPayment;
use App\Models\InventoryCount;
use App\Models\InventoryItem;
use App\Models\Spending;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Models\Wage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Shared database queries for the Reports Center.
 */
final class ReportCenterQuery
{
    public function __construct(
        public readonly int $organizationId,
        public readonly ?int $branchId,
        public readonly string $from,
        public readonly string $to,
        public readonly ?int $employeeId = null,
        public readonly ?string $status = null,
    ) {}

    public function cashUpNet(): float
    {
        return (float) $this->cashUpQuery()
            ->get()
            ->sum(fn (CashUp $c) => $c->netTotal());
    }

    public function cashUpGross(): float
    {
        return (float) $this->cashUpQuery()
            ->selectRaw('SUM(coins_total + notes_total + cards_total + online_orders_total) as total')
            ->value('total');
    }

    public function cardsTotal(): float
    {
        return (float) $this->cashUpQuery()->sum('cards_total');
    }

    public function onlineOrdersTotal(): float
    {
        return (float) $this->cashUpQuery()->sum('online_orders_total');
    }

    public function cashDifferenceTotal(): float
    {
        return (float) $this->cashUpQuery()->sum('platform_deductions_total');
    }

    /**
     * @return Collection<int, CashUp>
     */
    public function cashUps(): Collection
    {
        return $this->cashUpQuery()->with(['branch', 'creator'])->orderByDesc('cashup_date')->get();
    }

    public function manualIncome(): float
    {
        return (float) $this->incomeQuery()
            ->where('source', '!=', FinanceIncomeSource::CashUp->value)
            ->whereIn('status', [FinanceStatus::Approved->value, FinanceStatus::Paid->value])
            ->sum('gross_amount');
    }

    public function expensesTotal(): float
    {
        return (float) $this->expenseQuery()
            ->whereIn('status', [FinanceStatus::Approved->value, FinanceStatus::Paid->value, FinanceStatus::Draft->value])
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->sum('gross_amount');
    }

    public function billsTotal(): float
    {
        return (float) $this->billQuery()
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->sum('gross_amount');
    }

    public function billsPaid(): float
    {
        return (float) $this->billQuery()->where('status', BillStatus::Paid->value)->sum('gross_amount');
    }

    public function billsDue(): float
    {
        return (float) $this->billQuery()
            ->whereIn('status', [BillStatus::Approved->value, BillStatus::Pending->value, BillStatus::Overdue->value])
            ->sum('gross_amount');
    }

    public function payrollTotal(): float
    {
        return (float) $this->wageQuery()
            ->when($this->status, fn ($q) => $q->where('status', $this->mapWageStatus($this->status)))
            ->sum('gross_amount');
    }

    public function payrollPaid(): float
    {
        return (float) $this->wageQuery()->where('status', WageStatus::Paid->value)->sum('gross_amount');
    }

    public function payrollPending(): float
    {
        return (float) $this->wageQuery()
            ->whereIn('status', [WageStatus::Pending->value, WageStatus::Approved->value, WageStatus::Draft->value])
            ->sum('gross_amount');
    }

    public function supplierSpend(): float
    {
        return (float) $this->supplierPaymentQuery()->sum('gross_amount');
    }

    public function supplierOutstanding(): float
    {
        return (float) $this->supplierInvoiceQuery()
            ->where('status', SupplierInvoiceStatus::Pending->value)
            ->sum('gross_amount');
    }

    public function vatOutput(): float
    {
        return (float) $this->incomeQuery()
            ->whereIn('status', [FinanceStatus::Approved->value, FinanceStatus::Paid->value])
            ->sum('vat_amount');
    }

    public function vatInput(): float
    {
        $expenses = (float) $this->expenseQuery()
            ->whereIn('status', [FinanceStatus::Approved->value, FinanceStatus::Paid->value])
            ->sum('vat_amount');

        $bills = (float) $this->billQuery()->where('status', BillStatus::Paid->value)->sum('vat_amount');

        return $expenses + $bills;
    }

    public function revenueTotal(): float
    {
        return $this->cashUpNet() + $this->manualIncome();
    }

    public function profitEstimate(): float
    {
        return $this->revenueTotal()
            - $this->expensesTotal()
            - $this->billsPaid()
            - $this->payrollTotal()
            - $this->supplierSpend();
    }

    public function staffHours(): float
    {
        return $this->calculateHoursFromLogs($this->attendanceLogs());
    }

    public function lowStockCount(): int
    {
        return InventoryItem::query()
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereRaw('stock_limit > 0 AND stock_total_pcs <= stock_limit')
            ->count();
    }

    public function inventoryAdjustments(): int
    {
        return InventoryCount::query()
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereBetween('created_at', [$this->from.' 00:00:00', $this->to.' 23:59:59'])
            ->count();
    }

    /**
     * @return list<array{label: string, date: string, value: float}>
     */
    public function dailySeries(callable $valueResolver, int $maxPoints = 62): array
    {
        $start = Carbon::parse($this->from)->startOfDay();
        $end = Carbon::parse($this->to)->endOfDay();
        $days = min($maxPoints, (int) $start->diffInDays($end) + 1);
        $series = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $series[] = [
                'date' => $day->toDateString(),
                'label' => $day->format('d M'),
                'value' => round((float) $valueResolver($day), 2),
            ];
        }

        return $series;
    }

    /**
     * @return Collection<int, AttendanceLog>
     */
    public function attendanceLogs(): Collection
    {
        return AttendanceLog::query()
            ->with('user')
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->when($this->employeeId, fn ($q) => $q->where('user_id', $this->employeeId))
            ->whereBetween('logged_at', [$this->from.' 00:00:00', $this->to.' 23:59:59'])
            ->orderBy('logged_at')
            ->get();
    }

    /**
     * @return list<array{branch: string, revenue: float}>
     */
    public function branchRevenueComparison(): array
    {
        return CashUp::query()
            ->with('branch')
            ->where('organization_id', $this->organizationId)
            ->whereDate('cashup_date', '>=', $this->from)
            ->whereDate('cashup_date', '<=', $this->to)
            ->get()
            ->groupBy('branch_id')
            ->map(fn (Collection $rows, $branchId) => [
                'branch' => $rows->first()?->branch?->name ?? 'Branch #'.$branchId,
                'revenue' => round((float) $rows->sum(fn (CashUp $c) => $c->netTotal()), 2),
            ])
            ->sortByDesc('revenue')
            ->values()
            ->all();
    }

    /**
     * @return list<array{category: string, total: float}>
     */
    public function expensesByCategory(): array
    {
        return Spending::query()
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereBetween('spent_date', [$this->from, $this->to])
            ->selectRaw('category, SUM(gross_amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => ['category' => (string) $row->category, 'total' => round((float) $row->total, 2)])
            ->all();
    }

    /**
     * @return list<array{supplier: string, total: float}>
     */
    public function supplierSpendBreakdown(): array
    {
        return FinanceSupplierPayment::query()
            ->with('supplierInvoice.supplier')
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereBetween('payment_date', [$this->from, $this->to])
            ->get()
            ->groupBy(fn (FinanceSupplierPayment $p) => $p->supplierInvoice?->supplier_id ?? 0)
            ->map(fn (Collection $rows) => [
                'supplier' => $rows->first()?->supplierInvoice?->supplier?->name ?? 'Unknown',
                'total' => round((float) $rows->sum('gross_amount'), 2),
            ])
            ->sortByDesc('total')
            ->values()
            ->all();
    }

    /**
     * @return list<array{employee: string, hours: float, pay: float}>
     */
    public function employeePerformance(): array
    {
        $logs = $this->attendanceLogs();
        $staffRates = User::query()
            ->whereIn('id', $logs->pluck('user_id')->unique())
            ->pluck('hourly_rate', 'id');

        return $logs->groupBy('user_id')->map(function (Collection $userLogs, int $userId) use ($staffRates) {
            $hours = $this->calculateHoursFromLogs($userLogs);
            $rate = (float) ($staffRates[$userId] ?? 0);

            return [
                'employee' => $userLogs->first()?->user?->name ?? 'Staff #'.$userId,
                'hours' => $hours,
                'pay' => round($hours * $rate, 2),
            ];
        })->sortByDesc('hours')->values()->all();
    }

    private function cashUpQuery(): Builder
    {
        return CashUp::query()
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereDate('cashup_date', '>=', $this->from)
            ->whereDate('cashup_date', '<=', $this->to);
    }

    private function incomeQuery(): Builder
    {
        return FinanceIncomeEntry::query()
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereDate('income_date', '>=', $this->from)
            ->whereDate('income_date', '<=', $this->to);
    }

    private function expenseQuery(): Builder
    {
        return Spending::query()
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereDate('spent_date', '>=', $this->from)
            ->whereDate('spent_date', '<=', $this->to);
    }

    private function billQuery(): Builder
    {
        return Bill::query()
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->where(function ($q): void {
                $q->whereBetween('due_date', [$this->from, $this->to])
                    ->orWhereBetween('paid_date', [$this->from, $this->to]);
            });
    }

    private function wageQuery(): Builder
    {
        return Wage::query()
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->when($this->employeeId, fn ($q) => $q->where('user_id', $this->employeeId))
            ->where(function ($q): void {
                $q->whereBetween('created_at', [$this->from.' 00:00:00', $this->to.' 23:59:59'])
                    ->orWhereBetween('paid_date', [$this->from, $this->to]);
            });
    }

    private function supplierPaymentQuery(): Builder
    {
        return FinanceSupplierPayment::query()
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId))
            ->whereDate('payment_date', '>=', $this->from)
            ->whereDate('payment_date', '<=', $this->to);
    }

    private function supplierInvoiceQuery(): Builder
    {
        return SupplierInvoice::query()
            ->where('organization_id', $this->organizationId)
            ->when($this->branchId, fn ($q) => $q->where('branch_id', $this->branchId));
    }

    private function mapWageStatus(string $status): string
    {
        return match ($status) {
            'paid' => WageStatus::Paid->value,
            'approved', 'pending' => WageStatus::Approved->value,
            'draft' => WageStatus::Draft->value,
            default => $status,
        };
    }

    /**
     * @param  Collection<int, AttendanceLog>  $logs
     */
    private function calculateHoursFromLogs(Collection $logs): float
    {
        $seconds = 0;
        $in = null;

        foreach ($logs->sortBy('logged_at') as $log) {
            if ($log->type === AttendanceLogType::ClockIn) {
                $in = $log->logged_at;
            } elseif ($log->type === AttendanceLogType::ClockOut && $in !== null) {
                $seconds += $in->diffInSeconds($log->logged_at);
                $in = null;
            }
        }

        return round($seconds / 3600, 2);
    }
}
