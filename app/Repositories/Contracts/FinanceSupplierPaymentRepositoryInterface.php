<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\FinanceSupplierPayment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FinanceSupplierPaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForBranch(int $organizationId, ?int $branchId, int $perPage = 20): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function createPayment(array $data): FinanceSupplierPayment;

    public function sumForPeriod(int $organizationId, ?int $branchId, string $from, string $to): float;
}
