<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Supplier;
use App\Models\SupplierInvoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SupplierRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all suppliers for an organization.
     */
    public function getOrganizationSuppliers(int $organizationId, ?int $branchId = null): Collection;

    /**
     * Paginate suppliers.
     *
     * @param  array<string, mixed>  $filters
     */
    public function paginateSuppliers(int $organizationId, ?int $branchId = null, array|string|null $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Create a supplier.
     *
     * @param  array<string, mixed>  $data
     */
    public function createSupplier(array $data): Supplier;

    /**
     * Get invoices for a supplier.
     */
    public function getSupplierInvoices(int $supplierId): Collection;

    /**
     * Create an invoice.
     *
     * @param  array<string, mixed>  $data
     */
    public function createInvoice(array $data): SupplierInvoice;

    /**
     * Mark invoice as paid.
     */
    public function markInvoicePaid(int $invoiceId, string $paidDate): SupplierInvoice;

    /**
     * Get pending invoices.
     */
    public function getPendingInvoices(int $organizationId, ?int $branchId = null): Collection;

    public function paginateInvoices(int $organizationId, ?int $branchId = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator;

    public function pendingInvoiceCount(int $organizationId, ?int $branchId = null): int;

    public function outstandingAmount(int $organizationId, ?int $branchId = null): float;
}
