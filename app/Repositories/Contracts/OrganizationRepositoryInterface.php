<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface OrganizationRepositoryInterface extends BaseRepositoryInterface
{
    public function countAll(): int;

    public function countByStatus(string $status): int;

    public function countTrialing(): int;

    public function countActivePaid(): int;

    public function countExpiredSubscriptions(): int;

    /**
     * @return Collection<int, \App\Models\Organization>
     */
    public function latestWithRelations(int $limit = 8): Collection;

    /**
     * @param  array{search?: string|null, status?: string|null, sort?: string|null, direction?: string|null}  $filters
     */
    public function paginateFiltered(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function countActiveBranches(): int;

    public function countActiveStaff(): int;

    public function countPendingRequests(): int;

    /**
     * @return SupportCollection<int, object{month: string, total: int}>
     */
    public function monthlySignupCounts(int $months = 12): SupportCollection;
}
