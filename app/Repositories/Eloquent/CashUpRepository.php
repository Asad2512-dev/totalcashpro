<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\CashUpShift;
use App\Models\CashUp;
use App\Repositories\Contracts\CashUpRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

final class CashUpRepository extends BaseRepository implements CashUpRepositoryInterface
{
    public function __construct(CashUp $model)
    {
        parent::__construct($model);
    }

    public function findByDateShift(int $organizationId, int $branchId, Carbon|string $date, CashUpShift|string $shift): ?CashUp
    {
        $shiftValue = $shift instanceof CashUpShift ? $shift->value : $shift;
        $dateValue = $date instanceof Carbon ? $date->toDateString() : (string) $date;

        return $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->where('branch_id', $branchId)
            ->whereDate('cashup_date', $dateValue)
            ->where('shift', $shiftValue)
            ->first();
    }

    public function upsertForShift(array $attributes, bool $overwrite = false): CashUp
    {
        $existing = $this->findByDateShift(
            (int) $attributes['organization_id'],
            (int) $attributes['branch_id'],
            $attributes['cashup_date'],
            $attributes['shift'],
        );

        if ($existing !== null && ! $overwrite) {
            throw ValidationException::withMessages([
                'cashup' => 'A cash up already exists for this date and shift. Confirm overwrite to replace it.',
            ]);
        }

        if ($existing !== null) {
            $existing->update($attributes);

            return $existing->refresh();
        }

        return $this->create($attributes);
    }

    public function upsertCashUp(int $organizationId, int $branchId, string $date, CashUpShift $shift, array $data): CashUp
    {
        return $this->upsertForShift(array_merge($data, [
            'organization_id' => $organizationId,
            'branch_id' => $branchId,
            'cashup_date' => $date,
            'shift' => $shift->value,
        ]), true);
    }

    public function forRange(int $organizationId, ?int $branchId, Carbon $from, Carbon $to): Collection
    {
        return $this->forDateRange(
            $organizationId,
            $branchId,
            $from->toDateString(),
            $to->toDateString(),
        );
    }

    public function forDateRange(int $organizationId, ?int $branchId, string $startDate, string $endDate): Collection
    {
        return $this->model->newQuery()
            ->with(['branch', 'creator'])
            ->where('organization_id', $organizationId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('cashup_date', [$startDate, $endDate])
            ->orderByDesc('cashup_date')
            ->orderBy('shift')
            ->get();
    }

    public function sumNetForRange(int $organizationId, ?int $branchId, Carbon $from, Carbon $to): float
    {
        return (float) $this->forRange($organizationId, $branchId, $from, $to)
            ->sum(fn (CashUp $cashUp) => $cashUp->netTotal());
    }

    public function todayTotals(int $organizationId, ?int $branchId): array
    {
        return $this->calculateTotals(
            $this->scopedQuery($organizationId, $branchId)->whereDate('cashup_date', today()),
        );
    }

    public function weeklyTotals(int $organizationId, ?int $branchId): array
    {
        return $this->calculateTotals(
            $this->scopedQuery($organizationId, $branchId)->whereBetween('cashup_date', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString(),
            ]),
        );
    }

    public function monthlyTotals(int $organizationId, ?int $branchId): array
    {
        return $this->calculateTotals(
            $this->scopedQuery($organizationId, $branchId)->whereBetween('cashup_date', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ]),
        );
    }

    private function scopedQuery(int $organizationId, ?int $branchId): Builder
    {
        return $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));
    }

    /**
     * @return array<string, float>
     */
    private function calculateTotals(Builder $query): array
    {
        $result = $query->selectRaw('
            SUM(coins_total) as total_coins,
            SUM(notes_total) as total_notes,
            SUM(cards_total) as total_cards,
            SUM(expenses_total) as total_expenses,
            SUM(online_orders_total) as total_online_orders,
            SUM(platform_deductions_total) as total_platform_deductions
        ')->first();

        $coins = (float) ($result->total_coins ?? 0);
        $notes = (float) ($result->total_notes ?? 0);
        $cards = (float) ($result->total_cards ?? 0);
        $expenses = (float) ($result->total_expenses ?? 0);
        $online = (float) ($result->total_online_orders ?? 0);
        $deductions = (float) ($result->total_platform_deductions ?? 0);

        return [
            'coins' => $coins,
            'notes' => $notes,
            'cards' => $cards,
            'expenses' => $expenses,
            'online_orders' => $online,
            'platform_deductions' => $deductions,
            'grand_total' => $coins + $notes + $cards + $online - $expenses - $deductions,
        ];
    }
}
