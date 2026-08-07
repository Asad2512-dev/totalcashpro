<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\FinanceSupplierPayment;
use App\Repositories\Contracts\FinanceSupplierPaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class FinanceSupplierPaymentRepository extends BaseRepository implements FinanceSupplierPaymentRepositoryInterface
{
    public function __construct(FinanceSupplierPayment $model)
    {
        parent::__construct($model);
    }

    public function paginateForBranch(int $organizationId, ?int $branchId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['supplierInvoice.supplier', 'bankAccount'])
            ->where('organization_id', $organizationId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('payment_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createPayment(array $data): FinanceSupplierPayment
    {
        return $this->model->newQuery()->create($data);
    }

    public function sumForPeriod(int $organizationId, ?int $branchId, string $from, string $to): float
    {
        return (float) $this->model->newQuery()
            ->where('organization_id', $organizationId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('payment_date', [$from, $to])
            ->sum('gross_amount');
    }
}
