<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Spending;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SpendingRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForBranch(int $organizationId, ?int $branchId, array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function sumForPeriod(int $organizationId, ?int $branchId, string $from, string $to, ?string $status = null): float;

    /**
     * @param  array<string, mixed>  $data
     */
    public function createExpense(array $data): Spending;
}
