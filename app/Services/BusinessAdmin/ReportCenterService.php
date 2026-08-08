<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\ReportCompareMode;
use App\Enums\ReportDatePreset;
use App\Enums\ReportType;
use App\Enums\WageStatus;
use App\Models\Bill;
use App\Models\CashUp;
use App\Models\Spending;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Models\Wage;
use App\Repositories\Contracts\StaffRepositoryInterface;
use App\Support\Reports\ReportCenterCache;
use App\Support\Reports\ReportCenterFilter;
use App\Support\Reports\ReportCenterQuery;
use App\Support\Reports\ReportDateRangeResolver;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

final class ReportCenterService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly StaffRepositoryInterface $staff,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function build(User $user, ReportCenterFilter $filter): array
    {
        $orgId = (int) $user->organization_id;
        $version = ReportCenterCache::version($orgId);
        $cacheKey = sprintf(
            'report_center:v3:%d:%d:%s',
            $orgId,
            $version,
            md5(json_encode($filter->toQueryArray()) ?: ''),
        );

        $data = Cache::remember($cacheKey, now()->addSeconds(5), fn () => $this->compileData($user, $filter));

        $branchId = $filter->branchId ?? $this->branchContext->currentBranchId($user);

        return array_merge($data, [
            'generated_at' => now()->toIso8601String(),
            'branches' => $this->branchContext->resolveBranches($user),
            'employees' => $this->staff->activeStaff($orgId, $branchId),
            'date_presets' => ReportDatePreset::cases(),
            'report_types' => ReportType::cases(),
            'compare_modes' => ReportCompareMode::cases(),
            'statuses' => ['all', 'draft', 'approved', 'pending', 'paid', 'cancelled'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function compileData(User $user, ReportCenterFilter $filter): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $filter->branchId ?? $this->branchContext->currentBranchId($user);
        $range = ReportDateRangeResolver::resolve($filter->datePreset, $filter->from, $filter->to);

        $query = new ReportCenterQuery(
            $orgId,
            $branchId,
            $range['from'],
            $range['to'],
            $filter->employeeId,
            $filter->status,
        );

        $kpis = $this->kpis($query);
        $charts = $this->charts($query);
        $insights = $this->insights($query, $orgId, $branchId, $range['from'], $range['to']);
        $table = $this->table($filter->reportType, $query, $orgId, $branchId, $range['from'], $range['to'], $filter);
        $comparison = $this->comparison($user, $filter, $query, $orgId, $branchId, $range);

        $daysInRange = max(1, Carbon::parse($range['from'])->diffInDays(Carbon::parse($range['to'])) + 1);
        $weeksInRange = max(1, $daysInRange / 7);

        return [
            'range' => $range,
            'kpis' => $kpis,
            'charts' => $charts,
            'insights' => $insights,
            'comparison' => $comparison,
            'table' => $table,
            'summary' => $this->summary($filter->reportType, $query, $kpis),
            'growth' => $this->growthPercent((float) $kpis['revenue'], $comparison),
            'averages' => [
                'daily_revenue' => round($kpis['revenue'] / $daysInRange, 2),
                'weekly_revenue' => round($kpis['revenue'] / $weeksInRange, 2),
            ],
            'is_empty' => $table['rows'] === [],
            'empty_message' => $this->emptyMessage($filter->reportType),
        ];
    }

    /**
     * @return array<string, float|int>
     */
    private function kpis(ReportCenterQuery $query): array
    {
        return [
            'revenue' => $query->revenueTotal(),
            'expenses' => $query->expensesTotal(),
            'profit' => $query->profitEstimate(),
            'cash' => $query->cashUpNet(),
            'cards' => $query->cardsTotal(),
            'online_orders' => $query->onlineOrdersTotal(),
            'bills' => $query->billsDue(),
            'bills_paid' => $query->billsPaid(),
            'payroll' => $query->payrollTotal(),
            'payroll_paid' => $query->payrollPaid(),
            'payroll_pending' => $query->payrollPending(),
            'inventory_adjustments' => $query->inventoryAdjustments(),
            'low_stock' => $query->lowStockCount(),
            'supplier_payments' => $query->supplierSpend(),
            'supplier_outstanding' => $query->supplierOutstanding(),
            'attendance_hours' => $query->staffHours(),
            'vat_output' => $query->vatOutput(),
            'vat_input' => $query->vatInput(),
            'vat_due' => $query->vatOutput() - $query->vatInput(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function charts(ReportCenterQuery $query): array
    {
        $revenueTrend = $query->dailySeries(function (Carbon $day) use ($query) {
            $q = new ReportCenterQuery(
                $query->organizationId,
                $query->branchId,
                $day->toDateString(),
                $day->toDateString(),
            );

            return $q->revenueTotal();
        });

        $expenseTrend = $query->dailySeries(function (Carbon $day) use ($query) {
            $q = new ReportCenterQuery(
                $query->organizationId,
                $query->branchId,
                $day->toDateString(),
                $day->toDateString(),
            );

            return $q->expensesTotal();
        });

        $profitTrend = array_map(function (array $rev, array $exp) {
            return [
                'date' => $rev['date'],
                'label' => $rev['label'],
                'value' => round($rev['value'] - $exp['value'], 2),
            ];
        }, $revenueTrend, $expenseTrend);

        $attendanceTrend = $query->dailySeries(function (Carbon $day) use ($query) {
            $q = new ReportCenterQuery(
                $query->organizationId,
                $query->branchId,
                $day->toDateString(),
                $day->toDateString(),
            );

            return $q->staffHours();
        });

        $payrollTrend = $query->dailySeries(function (Carbon $day) use ($query) {
            return (float) Wage::query()
                ->where('organization_id', $query->organizationId)
                ->when($query->branchId, fn ($q) => $q->where('branch_id', $query->branchId))
                ->whereDate('paid_date', $day->toDateString())
                ->where('status', WageStatus::Paid->value)
                ->sum('gross_amount');
        });

        return [
            'revenue_trend' => $revenueTrend,
            'expenses_trend' => $expenseTrend,
            'profit_trend' => $profitTrend,
            'attendance_trend' => $attendanceTrend,
            'payroll_trend' => $payrollTrend,
            'cash_flow' => $profitTrend,
            'supplier_spend' => $query->supplierSpendBreakdown(),
            'expense_categories' => $query->expensesByCategory(),
            'branch_comparison' => $query->branchRevenueComparison(),
        ];
    }

    /**
     * @return list<array{title: string, value: string, tone: string}>
     */
    private function insights(ReportCenterQuery $query, int $orgId, ?int $branchId, string $from, string $to): array
    {
        $dailyRevenue = $query->dailySeries(fn (Carbon $day) => (new ReportCenterQuery($orgId, $branchId, $day->toDateString(), $day->toDateString()))->revenueTotal());
        $bestDay = collect($dailyRevenue)->sortByDesc('value')->first();
        $worstDay = collect($dailyRevenue)->filter(fn ($d) => $d['value'] > 0)->sortBy('value')->first();

        $topCategory = collect($query->expensesByCategory())->first();
        $topSupplier = collect($query->supplierSpendBreakdown())->first();
        $branches = collect($query->branchRevenueComparison());
        $bestBranch = $branches->first();
        $employees = collect($query->employeePerformance());
        $topEmployee = $employees->first();

        $largestDeduction = CashUp::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('cashup_date', [$from, $to])
            ->orderByDesc('platform_deductions_total')
            ->first();

        $topPayrollWeekTotal = Wage::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('paid_date', [$from, $to])
            ->whereNotNull('paid_date')
            ->get()
            ->groupBy(fn (Wage $wage) => $wage->paid_date?->format('o-W') ?? 'unknown')
            ->map(fn ($group) => (float) $group->sum('gross_amount'))
            ->sortDesc()
            ->first();

        return array_values(array_filter([
            $bestDay ? ['title' => 'Highest revenue day', 'value' => $bestDay['label'].' · £'.number_format($bestDay['value'], 2), 'tone' => 'success'] : null,
            $worstDay ? ['title' => 'Lowest revenue day', 'value' => $worstDay['label'].' · £'.number_format($worstDay['value'], 2), 'tone' => 'warning'] : null,
            $topCategory ? ['title' => 'Top expense category', 'value' => ucfirst($topCategory['category']).' · £'.number_format($topCategory['total'], 2), 'tone' => 'info'] : null,
            $topSupplier ? ['title' => 'Most spend supplier', 'value' => $topSupplier['supplier'].' · £'.number_format($topSupplier['total'], 2), 'tone' => 'info'] : null,
            $bestBranch ? ['title' => 'Best performing branch', 'value' => $bestBranch['branch'].' · £'.number_format($bestBranch['revenue'], 2), 'tone' => 'success'] : null,
            $topEmployee ? ['title' => 'Most active employee', 'value' => $topEmployee['employee'].' · '.$topEmployee['hours'].'h', 'tone' => 'neutral'] : null,
            $largestDeduction ? ['title' => 'Largest cash difference', 'value' => '£'.number_format((float) $largestDeduction->platform_deductions_total, 2).' · '.$largestDeduction->cashup_date?->format('d M Y'), 'tone' => 'danger'] : null,
            $topPayrollWeekTotal ? ['title' => 'Highest payroll week', 'value' => '£'.number_format((float) $topPayrollWeekTotal, 2), 'tone' => 'warning'] : null,
        ]));
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function table(
        ReportType $type,
        ReportCenterQuery $query,
        int $orgId,
        ?int $branchId,
        string $from,
        string $to,
        ReportCenterFilter $filter,
    ): array {
        return match ($type) {
            ReportType::CashUp, ReportType::Sales => $this->cashUpTable($query),
            ReportType::Attendance, ReportType::Staff => $this->attendanceTable($query),
            ReportType::Inventory => $this->inventoryTable($orgId, $branchId),
            ReportType::Payroll => $this->payrollTable($orgId, $branchId, $from, $to, $filter),
            ReportType::Expenses => $this->expensesTable($orgId, $branchId, $from, $to, $filter),
            ReportType::Bills => $this->billsTable($orgId, $branchId, $from, $to, $filter),
            ReportType::Suppliers => $this->suppliersTable($orgId, $branchId, $from, $to),
            ReportType::ProfitLoss => $this->profitLossTable($query),
            ReportType::CashFlow => $this->cashFlowTable($query),
            ReportType::Vat => $this->vatTable($query),
            default => $this->overviewTable($query),
        };
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function overviewTable(ReportCenterQuery $query): array
    {
        return [
            'columns' => ['Date', 'Branch', 'Shift', 'Net revenue', 'Cards', 'Online'],
            'rows' => $query->cashUps()->map(fn (CashUp $row) => [
                $row->cashup_date?->format('d M Y') ?? '—',
                $row->branch?->name ?? '—',
                $row->shift instanceof \BackedEnum ? $row->shift->value : (string) $row->shift,
                '£'.number_format($row->revenueTotal(), 2),
                '£'.number_format((float) $row->cards_total, 2),
                '£'.number_format((float) $row->online_orders_total, 2),
            ])->all(),
        ];
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function cashUpTable(ReportCenterQuery $query): array
    {
        return [
            'columns' => ['Date', 'Shift', 'Coins', 'Notes', 'Cards', 'Expenses', 'Net'],
            'rows' => $query->cashUps()->map(fn (CashUp $row) => [
                $row->cashup_date?->format('d M Y') ?? '—',
                $row->shift instanceof \BackedEnum ? $row->shift->value : (string) $row->shift,
                '£'.number_format((float) $row->coins_total, 2),
                '£'.number_format((float) $row->notes_total, 2),
                '£'.number_format((float) $row->cards_total, 2),
                '£'.number_format((float) $row->expenses_total, 2),
                '£'.number_format($row->revenueTotal(), 2),
            ])->all(),
        ];
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function attendanceTable(ReportCenterQuery $query): array
    {
        return [
            'columns' => ['Employee', 'Hours worked', 'Est. pay'],
            'rows' => collect($query->employeePerformance())->map(fn (array $row) => [
                $row['employee'],
                number_format($row['hours'], 2).'h',
                '£'.number_format($row['pay'], 2),
            ])->all(),
        ];
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function inventoryTable(int $orgId, ?int $branchId): array
    {
        $items = \App\Models\InventoryItem::query()
            ->with(['branch', 'category'])
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderBy('name')
            ->get();

        return [
            'columns' => ['Item', 'Category', 'Branch', 'Stock', 'Limit', 'Status'],
            'rows' => $items->map(fn ($item) => [
                $item->name,
                $item->category?->name ?? '—',
                $item->branch?->name ?? '—',
                (string) $item->stock_total_pcs,
                (string) $item->stock_limit,
                $item->isLowStock() ? 'Low stock' : 'OK',
            ])->all(),
        ];
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function payrollTable(int $orgId, ?int $branchId, string $from, string $to, ReportCenterFilter $filter): array
    {
        $rows = Wage::query()
            ->with('user')
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($filter->employeeId, fn ($q) => $q->where('user_id', $filter->employeeId))
            ->whereBetween('created_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->orderByDesc('created_at')
            ->get();

        return [
            'columns' => ['Staff', 'Hours', 'Gross', 'Status', 'Due', 'Paid'],
            'rows' => $rows->map(fn (Wage $w) => [
                $w->user?->name ?? '—',
                number_format((float) $w->hours_worked, 2).'h',
                '£'.number_format((float) $w->gross_amount, 2),
                $w->status instanceof \BackedEnum ? $w->status->label() : (string) $w->status,
                $w->payment_due_date?->format('d M Y') ?? '—',
                $w->paid_date?->format('d M Y') ?? '—',
            ])->all(),
        ];
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function expensesTable(int $orgId, ?int $branchId, string $from, string $to, ReportCenterFilter $filter): array
    {
        $rows = Spending::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($filter->status, fn ($q) => $q->where('status', $filter->status))
            ->whereBetween('spent_date', [$from, $to])
            ->orderByDesc('spent_date')
            ->get();

        return [
            'columns' => ['Title', 'Category', 'Net', 'VAT', 'Gross', 'Date', 'Status'],
            'rows' => $rows->map(fn (Spending $row) => [
                $row->title,
                $row->category,
                '£'.number_format((float) $row->net_amount, 2),
                '£'.number_format((float) $row->vat_amount, 2),
                '£'.number_format((float) $row->gross_amount, 2),
                $row->spent_date?->format('d M Y') ?? '—',
                $row->status?->label() ?? (string) $row->status,
            ])->all(),
        ];
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function billsTable(int $orgId, ?int $branchId, string $from, string $to, ReportCenterFilter $filter): array
    {
        $rows = Bill::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($filter->status, fn ($q) => $q->where('status', $filter->status))
            ->where(function ($q) use ($from, $to): void {
                $q->whereBetween('due_date', [$from, $to])->orWhereBetween('paid_date', [$from, $to]);
            })
            ->orderBy('due_date')
            ->get();

        return [
            'columns' => ['Title', 'Vendor', 'Gross', 'Due', 'Status', 'Paid'],
            'rows' => $rows->map(fn (Bill $row) => [
                $row->title,
                $row->vendor ?: '—',
                '£'.number_format((float) $row->gross_amount, 2),
                $row->due_date?->format('d M Y') ?? '—',
                $row->status->label(),
                $row->paid_date?->format('d M Y') ?? '—',
            ])->all(),
        ];
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function suppliersTable(int $orgId, ?int $branchId, string $from, string $to): array
    {
        $rows = SupplierInvoice::query()
            ->with('supplier')
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('invoice_date', [$from, $to])
            ->orderByDesc('invoice_date')
            ->get();

        return [
            'columns' => ['Invoice', 'Supplier', 'Gross', 'Status', 'Due', 'Paid'],
            'rows' => $rows->map(fn (SupplierInvoice $row) => [
                $row->invoice_no,
                $row->supplier?->name ?? '—',
                '£'.number_format((float) $row->gross_amount, 2),
                $row->status?->value ?? (string) $row->status,
                $row->due_date?->format('d M Y') ?? '—',
                $row->paid_date?->format('d M Y') ?? '—',
            ])->all(),
        ];
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function profitLossTable(ReportCenterQuery $query): array
    {
        return [
            'columns' => ['Line', 'Amount'],
            'rows' => [
                ['Revenue', '£'.number_format($query->revenueTotal(), 2)],
                ['Other income', '£'.number_format($query->manualIncome(), 2)],
                ['Expenses', '-£'.number_format($query->expensesTotal(), 2)],
                ['Bills paid', '-£'.number_format($query->billsPaid(), 2)],
                ['Payroll paid', '-£'.number_format($query->payrollPaid(), 2)],
                ['Supplier payments', '-£'.number_format($query->supplierSpend(), 2)],
                ['Net profit', '£'.number_format($query->profitEstimate(), 2)],
            ],
        ];
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function cashFlowTable(ReportCenterQuery $query): array
    {
        $in = $query->revenueTotal();
        $out = $query->expensesTotal() + $query->payrollPaid() + $query->supplierSpend();

        return [
            'columns' => ['Flow', 'Amount'],
            'rows' => [
                ['Money in', '£'.number_format($in, 2)],
                ['Money out', '£'.number_format($out, 2)],
                ['Net cash', '£'.number_format($in - $out, 2)],
            ],
        ];
    }

    /**
     * @return array{columns: list<string>, rows: list<array<int, string>>}
     */
    private function vatTable(ReportCenterQuery $query): array
    {
        return [
            'columns' => ['Type', 'Amount'],
            'rows' => [
                ['Output VAT', '£'.number_format($query->vatOutput(), 2)],
                ['Input VAT', '£'.number_format($query->vatInput(), 2)],
                ['VAT due', '£'.number_format($query->vatOutput() - $query->vatInput(), 2)],
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function comparison(User $user, ReportCenterFilter $filter, ReportCenterQuery $query, int $orgId, ?int $branchId, array $range): ?array
    {
        if ($filter->compareMode === ReportCompareMode::None) {
            return null;
        }

        if ($filter->compareMode === ReportCompareMode::PreviousPeriod) {
            $prev = ReportDateRangeResolver::previousPeriod($range['from'], $range['to']);
            $prevQuery = new ReportCenterQuery($orgId, $branchId, $prev['from'], $prev['to'], $filter->employeeId, $filter->status);

            return [
                'label' => 'Previous period',
                'range' => $prev,
                'kpis' => $this->kpis($prevQuery),
            ];
        }

        if ($filter->compareMode === ReportCompareMode::Branch && $filter->compareBranchId) {
            $compareQuery = new ReportCenterQuery($orgId, $filter->compareBranchId, $range['from'], $range['to'], $filter->employeeId, $filter->status);

            return [
                'label' => 'Compare branch',
                'kpis' => $this->kpis($compareQuery),
            ];
        }

        if ($filter->compareMode === ReportCompareMode::Employee && $filter->compareEmployeeId) {
            $compareQuery = new ReportCenterQuery($orgId, $branchId, $range['from'], $range['to'], $filter->compareEmployeeId, $filter->status);

            return [
                'label' => 'Compare employee',
                'kpis' => $this->kpis($compareQuery),
            ];
        }

        return null;
    }

    private function growthPercent(float $currentRevenue, ?array $comparison): ?float
    {
        if ($comparison === null || ! isset($comparison['kpis']['revenue'])) {
            return null;
        }

        $previous = (float) $comparison['kpis']['revenue'];

        if ($previous <= 0) {
            return null;
        }

        return round((($currentRevenue - $previous) / $previous) * 100, 1);
    }

    /**
     * @param  array<string, float|int>  $kpis
     * @return array<string, string>
     */
    private function summary(ReportType $type, ReportCenterQuery $query, array $kpis): array
    {
        return match ($type) {
            ReportType::ProfitLoss => ['headline' => 'Net profit', 'value' => '£'.number_format((float) $kpis['profit'], 2)],
            ReportType::CashFlow => ['headline' => 'Net cash', 'value' => '£'.number_format($query->revenueTotal() - $query->expensesTotal() - $query->payrollPaid() - $query->supplierSpend(), 2)],
            ReportType::Vat => ['headline' => 'VAT due', 'value' => '£'.number_format((float) $kpis['vat_due'], 2)],
            default => ['headline' => 'Total revenue', 'value' => '£'.number_format((float) $kpis['revenue'], 2)],
        };
    }

    private function emptyMessage(ReportType $type): string
    {
        return match ($type) {
            ReportType::CashUp, ReportType::Sales => 'No cash ups recorded for this period. Complete daily cash ups to populate sales reports.',
            ReportType::Attendance, ReportType::Staff => 'No attendance activity found. Staff clock-ins will appear here.',
            ReportType::Inventory => 'No inventory items found for the selected branch.',
            ReportType::Payroll => 'No payroll records in this period.',
            ReportType::Expenses => 'No expenses recorded. Add expenses in Finance to track spending.',
            ReportType::Bills => 'No bills due or paid in this period.',
            ReportType::Suppliers => 'No supplier invoices in this period.',
            default => 'No data for the selected filters. Try widening the date range or switching branch.',
        };
    }
}
