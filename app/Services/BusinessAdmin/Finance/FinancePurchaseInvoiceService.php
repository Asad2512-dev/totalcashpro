<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;
use App\Enums\FinanceStatus;
use App\Enums\SupplierInvoiceStatus;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Repositories\Contracts\FinanceBankAccountRepositoryInterface;
use App\Services\BusinessAdmin\BranchContext;
use App\Support\Finance\VatCalculator;
use Illuminate\Http\UploadedFile;

final class FinancePurchaseInvoiceService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly FinanceBankAccountRepositoryInterface $bankAccounts,
        private readonly FinanceAttachmentService $attachments,
    ) {}

    public function list(User $user, ?string $status = null): mixed
    {
        return SupplierInvoice::query()
            ->with(['supplier', 'branch'])
            ->where('organization_id', $user->organization_id)
            ->when($this->branchContext->currentBranchId($user), fn ($q, $branchId) => $q->where('branch_id', $branchId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('invoice_date')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * @return array<string, mixed>
     */
    public function formMeta(User $user): array
    {
        return [
            'suppliers' => \App\Models\Supplier::query()
                ->where('organization_id', $user->organization_id)
                ->orderBy('name')
                ->get(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(User $user, array $data, ?UploadedFile $invoice = null): SupplierInvoice
    {
        $branchId = $this->branchContext->requireBranchId($user);
        $amounts = $this->resolveAmounts($data);

        $record = SupplierInvoice::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'supplier_id' => $data['supplier_id'],
            'invoice_no' => $data['invoice_no'],
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'] ?? $data['invoice_date'],
            'amount' => $amounts['gross'],
            'net_amount' => $amounts['net'],
            'vat_amount' => $amounts['vat'],
            'gross_amount' => $amounts['gross'],
            'description' => $data['description'] ?? null,
            'status' => SupplierInvoiceStatus::Pending->value,
        ]);

        if ($invoice !== null) {
            $this->attachments->store($user, $record, $invoice);
        }

        return $record;
    }

    public function approve(User $user, int $invoiceId): SupplierInvoice
    {
        $invoice = $this->authorize($user, $invoiceId);
        $invoice->update([
            'status' => SupplierInvoiceStatus::Approved->value,
            'approved_at' => now(),
        ]);

        return $invoice->refresh();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{net: float, vat: float, gross: float}
     */
    private function resolveAmounts(array $data): array
    {
        $vatRate = (float) ($data['vat_rate'] ?? 20);

        if (! empty($data['net_amount'])) {
            return VatCalculator::fromNet((float) $data['net_amount'], $vatRate);
        }

        return VatCalculator::fromGross((float) $data['gross_amount'], $vatRate);
    }

    private function authorize(User $user, int $invoiceId): SupplierInvoice
    {
        $invoice = SupplierInvoice::query()->findOrFail($invoiceId);

        if ((int) $invoice->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        return $invoice;
    }
}
