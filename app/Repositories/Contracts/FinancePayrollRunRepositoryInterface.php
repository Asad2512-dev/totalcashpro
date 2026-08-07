<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\FinancePayrollRun;
use Illuminate\Database\Eloquent\Collection;

interface FinancePayrollRunRepositoryInterface extends BaseRepositoryInterface
{
    public function findForWeek(int $organizationId, int $branchId, string $weekStart): ?FinancePayrollRun;

    public function listForBranch(int $organizationId, ?int $branchId): Collection;

    /**
     * @param  array<string, mixed>  $data
     */
    public function createRun(array $data): FinancePayrollRun;
}
