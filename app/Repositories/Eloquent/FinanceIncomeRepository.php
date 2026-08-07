<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\FinanceIncomeSource;
use App\Enums\FinanceStatus;
use App\Models\FinanceIncomeEntry;
use App\Repositories\Contracts\FinanceIncomeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class FinanceIncomeRepository extends BaseRepository implements FinanceIncomeRepositoryInterface
{
    public function __construct(FinanceIncomeEntry $model)
    {
        parent::__construct($model);
    }

    public function paginateForBranch(int $organizationId, ?int $branchId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId);

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('income_date')->paginate($perPage)->withQueryString();
    }

    public function sumForPeriod(int $organizationId, ?int $branchId, string $from, string $to, ?string $status = null): float
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->whereDate('income_date', '>=', $from)
            ->whereDate('income_date', '<=', $to);

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

    public function sumManualForPeriod(int $organizationId, ?int $branchId, string $from, string $to, ?string $status = null): float
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->where('source', '!=', FinanceIncomeSource::CashUp->value)
            ->whereDate('income_date', '>=', $from)
            ->whereDate('income_date', '<=', $to);

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

    public function createEntry(array $data): FinanceIncomeEntry
    {
        return $this->model->newQuery()->create($data);
    }
}
