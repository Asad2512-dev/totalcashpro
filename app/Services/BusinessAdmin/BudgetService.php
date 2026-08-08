<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\BudgetCategory;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\User;
use App\Support\Reports\ReportCenterQuery;
use App\Support\Reports\ReportDateRangeResolver;
use Illuminate\Support\Carbon;

final class BudgetService implements ServiceInterface
{
    /**
     * @param  list<array{category: string, amount: float}>  $lines
     */
    public function store(User $user, array $data, array $lines): Budget
    {
        $budget = Budget::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $data['branch_id'] ?? null,
            'year' => (int) $data['year'],
            'month' => $data['month'] ?? null,
            'name' => $data['name'],
            'currency' => $data['currency'] ?? 'GBP',
            'created_by' => $user->id,
        ]);

        foreach ($lines as $line) {
            BudgetLine::query()->create([
                'budget_id' => $budget->id,
                'category' => $line['category'],
                'amount' => $line['amount'],
            ]);
        }

        return $budget->load('lines');
    }

    /**
     * @return list<array{category: string, budget: float, actual: float, variance: float, percent: float, status: string}>
     */
    public function varianceForPeriod(User $user, int $year, int $month, ?int $branchId = null): array
    {
        $budget = Budget::query()
            ->where('organization_id', $user->organization_id)
            ->where('year', $year)
            ->where('month', $month)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId), fn ($q) => $q->whereNull('branch_id'))
            ->with('lines')
            ->first();

        if ($budget === null) {
            return [];
        }

        $from = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $to = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        $query = new ReportCenterQuery((int) $user->organization_id, $branchId, $from, $to);

        $actuals = [
            BudgetCategory::Revenue->value => $query->revenueTotal(),
            BudgetCategory::FoodCost->value => $query->supplierSpend(),
            BudgetCategory::Wages->value => $query->payrollTotal(),
            BudgetCategory::Utilities->value => $this->categoryActual($query, 'utilities'),
            BudgetCategory::Rent->value => $this->categoryActual($query, 'rent'),
            BudgetCategory::Marketing->value => $this->categoryActual($query, 'marketing'),
            BudgetCategory::Other->value => $query->expensesTotal(),
        ];

        $rows = [];
        foreach ($budget->lines as $line) {
            $cat = $line->category instanceof BudgetCategory ? $line->category->value : (string) $line->category;
            $budgetAmt = (float) $line->amount;
            $actual = (float) ($actuals[$cat] ?? 0);
            $variance = round($actual - $budgetAmt, 2);
            $pct = $budgetAmt > 0 ? round(($variance / $budgetAmt) * 100, 1) : 0;
            $status = abs($variance) < 0.01 ? 'on_budget' : ($variance > 0 && $cat !== BudgetCategory::Revenue->value ? 'over_budget' : ($variance < 0 && $cat === BudgetCategory::Revenue->value ? 'under_budget' : ($variance > 0 ? 'over_budget' : 'under_budget')));

            $rows[] = [
                'category' => $cat,
                'budget' => $budgetAmt,
                'actual' => $actual,
                'variance' => $variance,
                'percent' => $pct,
                'status' => $status,
            ];
        }

        return $rows;
    }

    private function categoryActual(ReportCenterQuery $query, string $category): float
    {
        foreach ($query->expensesByCategory() as $row) {
            if (strtolower((string) $row['category']) === strtolower($category)) {
                return (float) $row['total'];
            }
        }

        return 0.0;
    }
}
