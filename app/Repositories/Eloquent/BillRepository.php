<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\BillStatus;
use App\Models\Bill;
use App\Repositories\Contracts\BillRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class BillRepository extends BaseRepository implements BillRepositoryInterface
{
    public function __construct(Bill $model)
    {
        parent::__construct($model);
    }

    public function paginateForBranch(int $organizationId, ?int $branchId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with('branch')
            ->where('organization_id', $organizationId);

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('due_date')->paginate($perPage)->withQueryString();
    }

    public function dueTotal(int $organizationId, ?int $branchId): float
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->whereIn('status', [
                BillStatus::Approved->value,
                BillStatus::Pending->value,
                BillStatus::Overdue->value,
            ]);

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        return (float) $query->sum('gross_amount');
    }

    public function createBill(array $data): Bill
    {
        return $this->model->newQuery()->create($data);
    }

    public function markOverdue(int $organizationId): int
    {
        return $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->whereIn('status', [BillStatus::Approved->value, BillStatus::Pending->value])
            ->whereDate('due_date', '<', now()->toDateString())
            ->update(['status' => BillStatus::Overdue->value]);
    }
}
