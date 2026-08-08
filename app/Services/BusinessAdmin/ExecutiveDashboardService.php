<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\ReportDatePreset;
use App\Models\Branch;
use App\Models\FinanceBankAccount;
use App\Models\PettyCashAccount;
use App\Models\User;
use App\Support\Reports\ReportCenterQuery;
use App\Support\Reports\ReportDateRangeResolver;
use Illuminate\Support\Carbon;

final class ExecutiveDashboardService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly BusinessAlertService $alerts,
        private readonly BusinessInsightService $insights,
        private readonly BudgetService $budgets,
        private readonly Finance\FinanceReportService $financeReports,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function build(
        User $user,
        ReportDatePreset $preset = ReportDatePreset::ThisMonth,
        ?string $from = null,
        ?string $to = null,
        ?int $branchId = null,
    ): array {
        $orgId = (int) $user->organization_id;
        $branchId ??= $this->branchContext->currentBranchId($user);
        $range = ReportDateRangeResolver::resolve($preset, $from, $to);
        $previous = ReportDateRangeResolver::previousPeriod($range['from'], $range['to']);

        $query = new ReportCenterQuery($orgId, $branchId, $range['from'], $range['to']);
        $prevQuery = new ReportCenterQuery($orgId, $branchId, $previous['from'], $previous['to']);

        $pnl = $this->financeReports->profitAndLoss(
            $user,
            Carbon::parse($range['from']),
            Carbon::parse($range['to']),
        );

        $revenue = $query->revenueTotal();
        $grossProfit = $query->grossProfitEstimate();
        $operatingExpenses = $query->expensesTotal() + $query->billsPaid();
        $netProfit = $query->profitEstimate();

        $kpis = [
            'revenue' => $this->compareMetric($revenue, $prevQuery->revenueTotal()),
            'gross_profit' => $this->compareMetric($grossProfit, $prevQuery->grossProfitEstimate()),
            'gross_margin' => $this->compareMetric(
                $revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0,
                $prevQuery->revenueTotal() > 0 ? round(($prevQuery->grossProfitEstimate() / $prevQuery->revenueTotal()) * 100, 1) : 0,
                isPercent: true,
            ),
            'operating_expenses' => $this->compareMetric($operatingExpenses, $prevQuery->expensesTotal() + $prevQuery->billsPaid()),
            'net_profit' => $this->compareMetric($netProfit, $prevQuery->profitEstimate()),
            'cash_position' => $this->compareMetric($this->liquidCash($orgId, $branchId), $this->liquidCash($orgId, $branchId)),
            'accounts_payable' => $this->compareMetric($query->billsDue() + $query->supplierOutstanding(), $prevQuery->billsDue() + $prevQuery->supplierOutstanding()),
            'inventory_value' => $this->compareMetric($query->inventoryValue(), $prevQuery->inventoryValue()),
            'payroll_due' => $this->compareMetric($query->payrollPending(), $prevQuery->payrollPending()),
            'vat_position' => $this->compareMetric($query->vatOutput() - $query->vatInput(), $prevQuery->vatOutput() - $prevQuery->vatInput()),
        ];

        return [
            'range' => $range,
            'previous_range' => $previous,
            'preset' => $preset,
            'branch_id' => $branchId,
            'kpis' => $kpis,
            'revenue_trend' => $query->dailySeries(fn (Carbon $day) => (new ReportCenterQuery(
                $orgId,
                $branchId,
                $day->toDateString(),
                $day->toDateString(),
            ))->revenueTotal()),
            'profit_summary' => $pnl,
            'cash_breakdown' => $this->cashBreakdown($orgId, $branchId),
            'branches' => $this->branchPerformance($user, $range['from'], $range['to']),
            'branch_health' => $this->branchHealthScores($user, $range['from'], $range['to']),
            'alerts' => $this->alerts->openForOrganization($user, $branchId, 15),
            'insights' => $this->insights->generate($user, $query, $prevQuery, $range),
            'budget_vs_actual' => $this->budgets->varianceForPeriod($user, (int) date('Y'), (int) date('n'), $branchId),
            'cost_control' => $this->costControl($query, $prevQuery),
        ];
    }

    /**
     * @return array{current: float|int, previous: float|int, difference: float, percent: ?float, trend: string}
     */
    public function compareMetric(float|int $current, float|int $previous, bool $isPercent = false): array
    {
        $difference = round((float) $current - (float) $previous, 2);
        $percent = null;
        if ((float) $previous != 0.0) {
            $percent = round(($difference / (float) $previous) * 100, 1);
        } elseif ($current > 0) {
            $percent = 100.0;
        }

        $trend = match (true) {
            abs($difference) < 0.01 => 'flat',
            $difference > 0 => 'up',
            default => 'down',
        };

        return [
            'current' => $isPercent ? $current : round((float) $current, 2),
            'previous' => $isPercent ? $previous : round((float) $previous, 2),
            'difference' => $difference,
            'percent' => $percent,
            'trend' => $trend,
        ];
    }

    private function liquidCash(int $orgId, ?int $branchId): float
    {
        $bank = (float) FinanceBankAccount::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('opening_balance');

        $petty = (float) PettyCashAccount::query()
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->sum('balance');

        return $bank + $petty;
    }

    /**
     * @return array<string, float>
     */
    private function cashBreakdown(int $orgId, ?int $branchId): array
    {
        return [
            'bank' => (float) FinanceBankAccount::query()
                ->where('organization_id', $orgId)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->sum('opening_balance'),
            'petty_cash' => (float) PettyCashAccount::query()
                ->where('organization_id', $orgId)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->sum('balance'),
            'total' => $this->liquidCash($orgId, $branchId),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function branchPerformance(User $user, string $from, string $to): array
    {
        $orgId = (int) $user->organization_id;
        $branches = Branch::query()->where('organization_id', $orgId)->get();
        $rows = [];

        foreach ($branches as $branch) {
            $q = new ReportCenterQuery($orgId, (int) $branch->id, $from, $to);
            $revenue = $q->revenueTotal();
            $gross = $q->grossProfitEstimate();
            $rows[] = [
                'branch_id' => $branch->id,
                'branch' => $branch->name,
                'revenue' => $revenue,
                'gross_profit' => $gross,
                'margin' => $revenue > 0 ? round(($gross / $revenue) * 100, 1) : 0,
                'expenses' => $q->expensesTotal(),
                'net_profit' => $q->profitEstimate(),
                'cash_variance' => $q->cashDifferenceTotal(),
                'inventory_value' => $q->inventoryValue(),
                'supplier_spend' => $q->supplierSpend(),
                'attendance_hours' => $q->staffHours(),
            ];
        }

        usort($rows, fn ($a, $b) => $b['revenue'] <=> $a['revenue']);

        return $rows;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function branchHealthScores(User $user, string $from, string $to): array
    {
        $performance = $this->branchPerformance($user, $from, $to);
        $maxRevenue = max(1, ...array_column($performance, 'revenue'));

        return array_map(function (array $row) use ($maxRevenue): array {
            $reasons = [];
            $warnings = [];
            $score = 50;

            if ($row['revenue'] > $maxRevenue * 0.8) {
                $score += 15;
                $reasons[] = 'Strong revenue';
            }
            if ($row['margin'] >= 30) {
                $score += 10;
                $reasons[] = 'Healthy margin';
            }
            if ($row['attendance_hours'] > 0) {
                $score += 5;
                $reasons[] = 'Staff attendance recorded';
            }
            if ($row['cash_variance'] > 20) {
                $score -= 10;
                $warnings[] = 'Cash variance above threshold';
            }
            if ($row['supplier_spend'] > $row['revenue'] * 0.5 && $row['revenue'] > 0) {
                $score -= 10;
                $warnings[] = 'High supplier cost ratio';
            }

            $score = max(0, min(100, $score));

            return array_merge($row, [
                'health_score' => $score,
                'reasons' => $reasons,
                'warnings' => $warnings,
            ]);
        }, $performance);
    }

    /**
     * @return array<string, array{current: float, previous: float}>
     */
    private function costControl(ReportCenterQuery $query, ReportCenterQuery $prev): array
    {
        return [
            'food_cost' => ['current' => $query->supplierSpend(), 'previous' => $prev->supplierSpend()],
            'wages' => ['current' => $query->payrollTotal(), 'previous' => $prev->payrollTotal()],
            'utilities' => ['current' => $this->categorySpend($query, 'utilities'), 'previous' => $this->categorySpend($prev, 'utilities')],
            'rent' => ['current' => $this->categorySpend($query, 'rent'), 'previous' => $this->categorySpend($prev, 'rent')],
            'other' => ['current' => $query->expensesTotal(), 'previous' => $prev->expensesTotal()],
        ];
    }

    private function categorySpend(ReportCenterQuery $query, string $category): float
    {
        foreach ($query->expensesByCategory() as $row) {
            if (strtolower((string) $row['category']) === strtolower($category)) {
                return (float) $row['total'];
            }
        }

        return 0.0;
    }
}
