<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface StaffRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForOrganization(int $organizationId, ?int $branchId = null, ?string $search = null, int $perPage = 15): LengthAwarePaginator;

    /**
     * @return Collection<int, User>
     */
    public function activeStaff(int $organizationId, ?int $branchId = null): Collection;

    public function findByPin(int $organizationId, string $pin): ?User;

    /**
     * @return Collection<int, User>
     */
    public function getOrganizationStaff(int $organizationId, ?int $branchId = null): Collection;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateOrganizationStaff(int $organizationId, ?int $branchId = null, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * @return Collection<int, User>
     */
    public function getActiveBranchStaff(int $organizationId, int $branchId): Collection;

    public function findByPinCode(int $organizationId, string $pinCode): ?User;
}
