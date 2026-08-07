<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\PurchaseOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PurchaseOrderRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForBranch(int $organizationId, ?int $branchId, array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findForOrganization(int $organizationId, int $purchaseOrderId): PurchaseOrder;

    public function nextPoNumber(int $organizationId): string;
}
