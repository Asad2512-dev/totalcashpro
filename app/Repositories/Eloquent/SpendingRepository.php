<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\FinanceStatus;
use App\Models\Spending;
use App\Repositories\Contracts\SpendingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class SpendingRepository extends BaseRepository implements SpendingRepositoryInterface
{
    public function __construct(Spending $model)
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

        return $query->orderByDesc('spent_date')->paginate($perPage)->withQueryString();
    }

    public function sumForPeriod(int $organizationId, ?int $branchId, string $from, string $to, ?string $status = null): float
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->whereBetween('spent_date', [$from, $to]);

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        if ($status !== null) {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', [
                FinanceStatus::Approved->value,
                FinanceStatus::Paid->value,
            ]);
        }

        return (float) $query->sum('gross_amount');
    }

    public function createExpense(array $data): Spending
    {
        return $this->model->newQuery()->create($data);
    }
}
