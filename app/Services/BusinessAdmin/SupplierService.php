<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\SupplierInvoiceStatus;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Repositories\Contracts\SupplierRepositoryInterface;

final class SupplierService implements ServiceInterface
{
    public function __construct(
        private readonly SupplierRepositoryInterface $suppliers,
        private readonly BranchContext $branchContext,
    ) {}

    public function list(User $user): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);

        $suppliers = Supplier::query()
            ->where('organization_id', $orgId)
            ->withCount('invoices')
            ->orderBy('name')
            ->get();

        $invoices = SupplierInvoice::query()
            ->with('supplier')
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('due_date')
            ->paginate(20)
            ->withQueryString();

        return [
            'suppliers' => $suppliers,
            'invoices' => $invoices,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeSupplier(User $user, array $data): Supplier
    {
        return Supplier::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $this->branchContext->currentBranchId($user),
            'name' => $data['name'],
            'contact_name' => $data['contact_name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'active',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeInvoice(User $user, array $data): SupplierInvoice
    {
        $supplier = Supplier::query()->findOrFail((int) $data['supplier_id']);

        if ((int) $supplier->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $branchId = $this->branchContext->requireBranchId($user);

        return SupplierInvoice::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'supplier_id' => $supplier->id,
            'invoice_no' => $data['invoice_no'],
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'amount' => $data['amount'],
            'description' => $data['description'] ?? null,
            'status' => SupplierInvoiceStatus::Pending->value,
        ]);
    }

    public function markInvoicePaid(User $user, int $invoiceId): SupplierInvoice
    {
        $invoice = SupplierInvoice::query()->findOrFail($invoiceId);

        if ((int) $invoice->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $invoice->update([
            'status' => SupplierInvoiceStatus::Paid->value,
            'paid_date' => now()->toDateString(),
        ]);

        return $invoice;
    }
}
