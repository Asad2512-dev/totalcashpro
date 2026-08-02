<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\RoleSlug;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\StaffRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class StaffRepository extends BaseRepository implements StaffRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function paginateForOrganization(int $organizationId, ?int $branchId = null, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->paginateOrganizationStaff(
            $organizationId,
            $branchId,
            ['search' => $search],
            $perPage,
        );
    }

    public function activeStaff(int $organizationId, ?int $branchId = null): Collection
    {
        $staffRoleId = Role::query()->where('slug', RoleSlug::Staff->value)->value('id');

        return $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->where('status', 'active')
            ->when($staffRoleId, fn ($q) => $q->where('role_id', $staffRoleId))
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderBy('name')
            ->get();
    }

    public function findByPin(int $organizationId, string $pin): ?User
    {
        return $this->findByPinCode($organizationId, $pin);
    }

    public function getOrganizationStaff(int $organizationId, ?int $branchId = null): Collection
    {
        return $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->whereHas('role', fn ($q) => $q->whereIn('slug', [RoleSlug::Admin->value, RoleSlug::Staff->value]))
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->with(['role', 'branch'])
            ->orderBy('name')
            ->get();
    }

    public function paginateOrganizationStaff(int $organizationId, ?int $branchId = null, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $staffRoleId = Role::query()->where('slug', RoleSlug::Staff->value)->value('id');

        return $this->model->newQuery()
            ->with(['branch', 'role'])
            ->where('organization_id', $organizationId)
            ->when($staffRoleId, fn ($q) => $q->where('role_id', $staffRoleId))
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when(! empty($filters['search']), function ($q) use ($filters): void {
                $search = $filters['search'];
                $q->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('pin_code', 'like', "%{$search}%");
                });
            })
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getActiveBranchStaff(int $organizationId, int $branchId): Collection
    {
        return $this->activeStaff($organizationId, $branchId);
    }

    public function findByPinCode(int $organizationId, string $pinCode): ?User
    {
        return $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->where('pin_code', $pinCode)
            ->where('status', 'active')
            ->first();
    }
}
