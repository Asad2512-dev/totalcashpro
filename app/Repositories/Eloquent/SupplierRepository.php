<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\SupplierInvoiceStatus;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    public function getOrganizationSuppliers(int $organizationId, ?int $branchId = null): Collection
    {
        $query = $this->model->newQuery()
            ->where('organization_id', $organizationId);

        if ($branchId !== null) {
            $query->where(function ($builder) use ($branchId): void {
                $builder->where('branch_id', $branchId)
                    ->orWhereNull('branch_id');
            });
        }

        return $query->orderBy('name')->get();
    }

    public function paginateSuppliers(int $organizationId, ?int $branchId = null, array|string|null $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        if (is_string($filters) || $filters === null) {
            $filters = ['search' => $filters];
        }

        $query = $this->model->newQuery()
            ->with('branch')
            ->where('organization_id', $organizationId);

        if ($branchId !== null) {
            $query->where(function ($builder) use ($branchId): void {
                $builder->where('branch_id', $branchId)
                    ->orWhereNull('branch_id');
            });
        }

        if (! empty($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('phone', 'like', $search);
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createSupplier(array $data): Supplier
    {
        return $this->model->newQuery()->create($data);
    }

    public function getSupplierInvoices(int $supplierId): Collection
    {
        return SupplierInvoice::query()
            ->where('supplier_id', $supplierId)
            ->orderBy('invoice_date', 'desc')
            ->get();
    }

    public function createInvoice(array $data): SupplierInvoice
    {
        return SupplierInvoice::query()->create($data);
    }

    public function markInvoicePaid(int $invoiceId, string $paidDate): SupplierInvoice
    {
        $invoice = SupplierInvoice::query()->findOrFail($invoiceId);
        $invoice->update([
            'status' => SupplierInvoiceStatus::Paid->value,
            'paid_date' => $paidDate,
        ]);

        return $invoice->refresh();
    }

    public function getPendingInvoices(int $organizationId, ?int $branchId = null): Collection
    {
        $query = SupplierInvoice::query()
            ->where('organization_id', $organizationId)
            ->where('status', SupplierInvoiceStatus::Pending->value);

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        return $query->with('supplier')->orderBy('due_date')->get();
    }

    public function paginateInvoices(int $organizationId, ?int $branchId = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        return SupplierInvoice::query()
            ->with(['supplier', 'branch'])
            ->where('organization_id', $organizationId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('invoice_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function pendingInvoiceCount(int $organizationId, ?int $branchId = null): int
    {
        return $this->getPendingInvoices($organizationId, $branchId)->count();
    }

    public function outstandingAmount(int $organizationId, ?int $branchId = null): float
    {
        return (float) $this->getPendingInvoices($organizationId, $branchId)->sum('amount');
    }
}
