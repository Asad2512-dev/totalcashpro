<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function sumPaid(?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): float;

    public function sumPaidToday(): float;

    public function countPaidToday(): int;

    /**
     * @return Collection<int, \App\Models\Payment>
     */
    public function latest(int $limit = 8): Collection;

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     */
    public function paginateFiltered(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @return SupportCollection<int, object{month: string, total: float|int|string}>
     */
    public function monthlyRevenue(int $months = 12): SupportCollection;
}
