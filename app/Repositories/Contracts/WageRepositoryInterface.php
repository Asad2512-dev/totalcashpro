<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Wage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface WageRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get wages for a user.
     */
    public function getUserWages(int $userId): Collection;

    /**
     * Get wages for a branch.
     */
    public function getBranchWages(int $organizationId, int $branchId, ?string $status = null): Collection;

    /**
     * Paginate wages.
     *
     * @param  array<string, mixed>  $filters
     */
    public function paginateWages(int $organizationId, ?int $branchId = null, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Create a wage record.
     *
     * @param  array<string, mixed>  $data
     */
    public function createWage(array $data): Wage;

    /**
     * Mark wage as paid.
     */
    public function markWagePaid(int $wageId, string $paidDate): Wage;

    /**
     * Get pending wages.
     */
    public function getPendingWages(int $organizationId, ?int $branchId = null): Collection;

    /**
     * Calculate total pending wages amount.
     */
    public function totalPendingAmount(int $organizationId, ?int $branchId = null): float;

    public function paginateForOrganization(int $organizationId, ?int $branchId = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator;
}
