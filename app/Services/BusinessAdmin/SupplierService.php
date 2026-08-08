<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\SupplierInvoiceStatus;
use App\Enums\SupplierStatus;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Validation\ValidationException;

final class SupplierService implements ServiceInterface
{
    public function __construct(
        private readonly SupplierRepositoryInterface $suppliers,
        private readonly BranchContext $branchContext,
    ) {}

    public function list(User $user, ?string $search = null, ?string $status = null): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);

        $query = Supplier::query()
            ->where('organization_id', $orgId)
            ->withCount('invoices')
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('trading_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $invoices = SupplierInvoice::query()
            ->with('supplier')
            ->where('organization_id', $orgId)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('due_date')
            ->paginate(20)
            ->withQueryString();

        return [
            'suppliers' => $query->get(),
            'invoices' => $invoices,
        ];
    }

    public function find(User $user, Supplier $supplier): Supplier
    {
        abort_unless((int) $supplier->organization_id === (int) $user->organization_id, 403);

        return $supplier->load([
            'contacts', 'products.inventoryItem', 'invoices',
            'purchaseOrders' => fn ($q) => $q->latest()->limit(10),
        ]);
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
            'trading_name' => $data['trading_name'] ?? null,
            'contact_name' => $data['contact_name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'postcode' => $data['postcode'] ?? null,
            'website' => $data['website'] ?? null,
            'tax_number' => $data['tax_number'] ?? null,
            'payment_terms' => $data['payment_terms'] ?? null,
            'currency' => $data['currency'] ?? 'GBP',
            'lead_time_days' => $data['lead_time_days'] ?? 0,
            'min_order_value' => $data['min_order_value'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'status' => SupplierStatus::Active->value,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateSupplier(User $user, Supplier $supplier, array $data): Supplier
    {
        abort_unless((int) $supplier->organization_id === (int) $user->organization_id, 403);

        $supplier->update(collect($data)->only([
            'name', 'trading_name', 'contact_name', 'email', 'phone', 'address', 'postcode',
            'website', 'tax_number', 'payment_terms', 'currency', 'lead_time_days',
            'min_order_value', 'notes', 'status',
        ])->all());

        return $supplier->refresh();
    }

    public function archive(User $user, Supplier $supplier): Supplier
    {
        abort_unless((int) $supplier->organization_id === (int) $user->organization_id, 403);

        $supplier->update(['status' => SupplierStatus::Archived->value]);

        return $supplier;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeContact(User $user, Supplier $supplier, array $data): SupplierContact
    {
        abort_unless((int) $supplier->organization_id === (int) $user->organization_id, 403);

        if (! empty($data['is_primary'])) {
            SupplierContact::query()
                ->where('supplier_id', $supplier->id)
                ->update(['is_primary' => false]);
        }

        return SupplierContact::query()->create([
            'organization_id' => $user->organization_id,
            'supplier_id' => $supplier->id,
            'name' => $data['name'],
            'role' => $data['role'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'is_primary' => (bool) ($data['is_primary'] ?? false),
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

        $exists = SupplierInvoice::query()
            ->where('organization_id', $user->organization_id)
            ->where('supplier_id', $supplier->id)
            ->where('invoice_no', $data['invoice_no'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'invoice_no' => 'This invoice number already exists for this supplier.',
            ]);
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
            'gross_amount' => $data['amount'],
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
            'amount_paid' => $invoice->gross_amount,
        ]);

        return $invoice;
    }
}
