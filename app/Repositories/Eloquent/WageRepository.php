<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\WageStatus;
use App\Models\Wage;
use App\Repositories\Contracts\WageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class WageRepository extends BaseRepository implements WageRepositoryInterface
{
    public function __construct(Wage $model)
    {
        parent::__construct($model);
    }

    public function getUserWages(int $userId): Collection
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getBranchWages(int $organizationId, int $branchId, ?string $status = null): Collection
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->where('branch_id', $branchId);

        if ($status !== null) {
            $query->where('status', $status);
        }

        return $query->with('user')->orderBy('created_at', 'desc')->get();
    }

    public function paginateWages(int $organizationId, ?int $branchId = null, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId);

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->with(['user', 'branch'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createWage(array $data): Wage
    {
        return $this->model->newQuery()->create($data);
    }

    public function markWagePaid(int $wageId, string $paidDate): Wage
    {
        $wage = $this->model->newQuery()->findOrFail($wageId);
        $wage->update([
            'status' => WageStatus::Paid->value,
            'paid_date' => $paidDate,
        ]);

        return $wage->refresh();
    }

    public function getPendingWages(int $organizationId, ?int $branchId = null): Collection
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->where('status', WageStatus::Pending->value);

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        return $query->with('user')->orderBy('created_at')->get();
    }

    public function totalPendingAmount(int $organizationId, ?int $branchId = null): float
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->where('status', WageStatus::Pending->value);

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        return (float) $query->sum('amount');
    }

    public function paginateForOrganization(int $organizationId, ?int $branchId = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->paginateWages(
            $organizationId,
            $branchId,
            $status ? ['status' => $status] : [],
            $perPage,
        );
    }
}
