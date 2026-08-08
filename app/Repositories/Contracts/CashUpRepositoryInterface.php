<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\CashUpShift;
use App\Models\CashUp;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface CashUpRepositoryInterface extends BaseRepositoryInterface
{
    public function findByDateShift(
        int $organizationId,
        int $branchId,
        Carbon|string $date,
        CashUpShift|string $shift,
        ?int $cashDrawerId = null,
    ): ?CashUp;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function upsertForShift(array $attributes, bool $overwrite = false): CashUp;

    /**
     * @param  array<string, mixed>  $data
     */
    public function upsertCashUp(int $organizationId, int $branchId, string $date, CashUpShift $shift, array $data): CashUp;

    /**
     * @return Collection<int, CashUp>
     */
    public function forRange(int $organizationId, ?int $branchId, Carbon $from, Carbon $to): Collection;

    /**
     * @return Collection<int, CashUp>
     */
    public function forDateRange(int $organizationId, ?int $branchId, string $startDate, string $endDate): Collection;

    public function sumNetForRange(int $organizationId, ?int $branchId, Carbon $from, Carbon $to): float;

    /**
     * @return array<string, float>
     */
    public function todayTotals(int $organizationId, ?int $branchId): array;

    /**
     * @return array<string, float>
     */
    public function weeklyTotals(int $organizationId, ?int $branchId): array;

    /**
     * @return array<string, float>
     */
    public function monthlyTotals(int $organizationId, ?int $branchId): array;
}
