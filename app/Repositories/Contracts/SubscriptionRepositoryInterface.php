<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SubscriptionRepositoryInterface extends BaseRepositoryInterface
{
    public function countByStatus(string $status): int;

    public function countExpiringSoon(int $days = 7): int;

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     */
    public function paginateFiltered(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @return Collection<int, object{month: string, total: int}>
     */
    public function monthlyNewCounts(int $months = 12): Collection;
}
