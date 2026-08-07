<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\PurchaseOrder;
use App\Repositories\Contracts\PurchaseOrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class PurchaseOrderRepository extends BaseRepository implements PurchaseOrderRepositoryInterface
{
    public function __construct(PurchaseOrder $model)
    {
        parent::__construct($model);
    }

    public function paginateForBranch(int $organizationId, ?int $branchId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['supplier', 'branch', 'lines'])
            ->where('organization_id', $organizationId);

        if ($branchId !== null) {
            $query->where('branch_id', $branchId);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage)->withQueryString();
    }

    public function findForOrganization(int $organizationId, int $purchaseOrderId): PurchaseOrder
    {
        return $this->model->newQuery()
            ->with(['supplier', 'branch', 'lines.inventoryItem', 'goodsReceivedNotes.lines', 'supplierInvoice'])
            ->where('organization_id', $organizationId)
            ->findOrFail($purchaseOrderId);
    }

    public function nextPoNumber(int $organizationId): string
    {
        $count = $this->model->newQuery()->where('organization_id', $organizationId)->count() + 1;

        return 'PO-'.str_pad((string) $count, 5, '0', STR_PAD_LEFT);
    }
}
