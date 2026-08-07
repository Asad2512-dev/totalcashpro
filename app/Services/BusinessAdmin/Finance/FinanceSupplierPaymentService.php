<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;
use App\Enums\FinanceStatus;
use App\Enums\SupplierInvoiceStatus;
use App\Models\FinanceSupplierPayment;
use App\Models\SupplierInvoice;
use App\Models\User;
use App\Repositories\Contracts\FinanceBankAccountRepositoryInterface;
use App\Repositories\Contracts\FinanceSupplierPaymentRepositoryInterface;
use App\Services\BusinessAdmin\BranchContext;
use App\Support\Finance\VatCalculator;
use Illuminate\Http\UploadedFile;

final class FinanceSupplierPaymentService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly FinanceSupplierPaymentRepositoryInterface $payments,
        private readonly FinanceBankAccountRepositoryInterface $bankAccounts,
        private readonly FinanceAttachmentService $attachments,
    ) {}

    public function list(User $user): mixed
    {
        return $this->payments->paginateForBranch(
            (int) $user->organization_id,
            $this->branchContext->currentBranchId($user),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function formMeta(User $user): array
    {
        $orgId = (int) $user->organization_id;
        $branchId = $this->branchContext->currentBranchId($user);

        return [
            'bank_accounts' => $this->bankAccounts->forOrganization($orgId, $branchId),
            'invoices' => SupplierInvoice::query()
                ->with('supplier')
                ->where('organization_id', $orgId)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereNotIn('status', [SupplierInvoiceStatus::Paid->value, SupplierInvoiceStatus::Cancelled->value])
                ->orderBy('due_date')
                ->get(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(User $user, array $data, ?UploadedFile $receipt = null): FinanceSupplierPayment
    {
        $branchId = $this->branchContext->requireBranchId($user);
        $invoice = SupplierInvoice::query()->findOrFail((int) $data['supplier_invoice_id']);

        if ((int) $invoice->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $amounts = ! empty($data['gross_amount'])
            ? VatCalculator::fromGross((float) $data['gross_amount'], (float) ($data['vat_rate'] ?? 20))
            : [
                'net' => (float) $invoice->net_amount,
                'vat' => (float) $invoice->vat_amount,
                'gross' => (float) $invoice->gross_amount,
            ];

        $payment = $this->payments->createPayment([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'supplier_invoice_id' => $invoice->id,
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'net_amount' => $amounts['net'],
            'vat_amount' => $amounts['vat'],
            'gross_amount' => $amounts['gross'],
            'payment_date' => $data['payment_date'],
            'reference' => $data['reference'] ?? null,
            'payment_method' => $data['payment_method'] ?? 'bank_transfer',
            'status' => FinanceStatus::Paid->value,
            'created_by' => $user->id,
        ]);

        $paidTotal = (float) $invoice->amount_paid + $amounts['gross'];
        $gross = (float) $invoice->gross_amount;
        $newStatus = $paidTotal >= $gross - 0.01
            ? SupplierInvoiceStatus::Paid
            : SupplierInvoiceStatus::PartiallyPaid;

        $invoice->update([
            'status' => $newStatus->value,
            'amount_paid' => round($paidTotal, 2),
            'paid_date' => $newStatus === SupplierInvoiceStatus::Paid ? $data['payment_date'] : $invoice->paid_date,
        ]);

        if ($receipt !== null) {
            $this->attachments->store($user, $payment, $receipt);
        }

        return $payment;
    }
}
