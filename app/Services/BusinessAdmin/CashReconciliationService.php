<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\CashDrawerMovementType;
use App\Models\CashDrawerTransaction;
use Illuminate\Support\Collection;

final class CashReconciliationService implements ServiceInterface
{
    /**
     * @param  Collection<int, CashDrawerTransaction>|null  $movements
     * @return array{expected_cash: float, actual_cash: float, variance: float, cash_sales: float}
     */
    public function reconcile(
        float $openingFloat,
        float $actualCash,
        float $cashExpenses = 0.0,
        float $withdrawals = 0.0,
        float $deposits = 0.0,
        float $refunds = 0.0,
        ?float $cashSales = null,
        ?Collection $movements = null,
    ): array {
        if ($cashSales === null) {
            $cashSales = max(0, round($actualCash - $openingFloat + $cashExpenses, 2));
        }

        $expected = round(
            $openingFloat + $cashSales - $cashExpenses - $withdrawals - $refunds + $deposits,
            2,
        );

        if ($movements !== null && $movements->isNotEmpty()) {
            $ledgerDelta = (float) $movements
                ->reject(fn (CashDrawerTransaction $m) => $m->type === CashDrawerMovementType::Sale)
                ->sum(fn (CashDrawerTransaction $m) => $m->signedAmount());

            $expected = round($openingFloat + $cashSales + $ledgerDelta, 2);
        }

        $variance = round($actualCash - $expected, 2);

        return [
            'expected_cash' => $expected,
            'actual_cash' => round($actualCash, 2),
            'variance' => $variance,
            'cash_sales' => round($cashSales, 2),
        ];
    }

    public function requiresVarianceReason(float $variance, float $threshold): bool
    {
        return abs($variance) > abs($threshold);
    }

    public function varianceLabel(float $variance): string
    {
        if (abs($variance) < 0.005) {
            return 'Balanced';
        }

        return $variance > 0
            ? sprintf('£%s Over', number_format($variance, 2))
            : sprintf('-£%s Short', number_format(abs($variance), 2));
    }

    /**
     * @param  list<array{type: string, amount: float}>  $movementSummary
     */
    public function sumMovementsByType(array $movementSummary, CashDrawerMovementType $type): float
    {
        return (float) collect($movementSummary)
            ->where('type', $type->value)
            ->sum('amount');
    }
}
