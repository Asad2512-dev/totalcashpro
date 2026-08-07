<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Bill;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BillRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForBranch(int $organizationId, ?int $branchId, array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function dueTotal(int $organizationId, ?int $branchId): float;

    /**
     * @param  array<string, mixed>  $data
     */
    public function createBill(array $data): Bill;

    public function markOverdue(int $organizationId): int;
}
