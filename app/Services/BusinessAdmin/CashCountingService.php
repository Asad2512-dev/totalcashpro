<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;

final class CashCountingService implements ServiceInterface
{
  /**
   * @param  list<array{coin?: string, note?: string, value: float, qty?: int, amount?: float, is_qty?: bool}>  $rows
   */
    public function sumDenominationRows(array $rows): float
    {
        return round((float) collect($rows)->sum(function (array $row): float {
            if (($row['is_qty'] ?? true) === false) {
                return (float) ($row['amount'] ?? 0);
            }

            return ((float) ($row['value'] ?? 0)) * (int) ($row['qty'] ?? 0);
        }), 2);
    }

    /**
     * @param  list<array{coin: string, qty: int}>  $rows
     * @param  list<array{coin: string, value: float}>  $definitions
     */
    public function sumCoins(array $rows, array $definitions): float
    {
        $map = collect($definitions)->keyBy('coin');

        return round((float) collect($rows)->sum(function (array $row) use ($map): float {
            return ((float) ($map[$row['coin']]['value'] ?? 0)) * (int) ($row['qty'] ?? 0);
        }), 2);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  list<array{note: string, value: float, is_qty: bool}>  $definitions
     */
    public function sumNotes(array $rows, array $definitions, bool $excludeFloat = true): float
    {
        $map = collect($definitions)->keyBy('note');

        return round((float) collect($rows)->sum(function (array $row) use ($map, $excludeFloat): float {
            $note = (string) ($row['note'] ?? '');
            if ($excludeFloat && $note === 'Extra Coin (Float)') {
                return 0.0;
            }

            if (($row['is_qty'] ?? true) === false) {
                return (float) ($row['amount'] ?? 0);
            }

            return ((float) ($map[$note]['value'] ?? 0)) * (int) ($row['qty'] ?? 0);
        }), 2);
    }

    /**
     * @param  list<array{coin?: string, note?: string, qty: int, value: float}>  $count
     * @return list<array{coin?: string, note?: string, qty: int, value: float, total: float}>
     */
    public function expandCount(array $count): array
    {
        return collect($count)->map(function (array $row): array {
            $qty = (int) ($row['qty'] ?? 0);
            $value = (float) ($row['value'] ?? 0);

            return array_merge($row, [
                'qty' => $qty,
                'value' => $value,
                'total' => round($qty * $value, 2),
            ]);
        })->all();
    }
}
