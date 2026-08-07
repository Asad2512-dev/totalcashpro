<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\FinancePayrollRun;
use App\Repositories\Contracts\FinancePayrollRunRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class FinancePayrollRunRepository extends BaseRepository implements FinancePayrollRunRepositoryInterface
{
    public function __construct(FinancePayrollRun $model)
    {
        parent::__construct($model);
    }

    public function findForWeek(int $organizationId, int $branchId, string $weekStart): ?FinancePayrollRun
    {
        return $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->where('branch_id', $branchId)
            ->whereDate('week_start', $weekStart)
            ->first();
    }

    public function listForBranch(int $organizationId, ?int $branchId): Collection
    {
        return $this->model->newQuery()
            ->withCount('wages')
            ->where('organization_id', $organizationId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('week_start')
            ->limit(12)
            ->get();
    }

    public function createRun(array $data): FinancePayrollRun
    {
        return $this->model->newQuery()->create($data);
    }
}
