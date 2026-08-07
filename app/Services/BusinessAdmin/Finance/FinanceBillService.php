<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;
use App\Enums\BillStatus;
use App\Models\Bill;
use App\Models\User;
use App\Repositories\Contracts\BillRepositoryInterface;
use App\Repositories\Contracts\FinanceBankAccountRepositoryInterface;
use App\Services\BusinessAdmin\BranchContext;
use App\Support\Finance\VatCalculator;
use Illuminate\Http\UploadedFile;

final class FinanceBillService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly BillRepositoryInterface $bills,
        private readonly FinanceBankAccountRepositoryInterface $bankAccounts,
        private readonly FinanceAttachmentService $attachments,
    ) {}

    public function list(User $user, ?string $status = null): mixed
    {
        $this->bills->markOverdue((int) $user->organization_id);
        $filters = $status ? ['status' => $status] : [];

        return $this->bills->paginateForBranch(
            (int) $user->organization_id,
            $this->branchContext->currentBranchId($user),
            $filters,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function formMeta(User $user): array
    {
        return [
            'bank_accounts' => $this->bankAccounts->forOrganization(
                (int) $user->organization_id,
                $this->branchContext->currentBranchId($user),
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(User $user, array $data, ?UploadedFile $invoice = null): Bill
    {
        $branchId = $this->branchContext->requireBranchId($user);
        $amounts = $this->resolveAmounts($data);

        $bill = $this->bills->createBill([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'title' => $data['title'],
            'vendor' => $data['vendor'] ?? null,
            'category' => $data['category'] ?? 'other',
            'amount' => $amounts['gross'],
            'net_amount' => $amounts['net'],
            'vat_amount' => $amounts['vat'],
            'gross_amount' => $amounts['gross'],
            'due_date' => $data['due_date'],
            'status' => BillStatus::Draft->value,
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $user->id,
        ]);

        if ($invoice !== null) {
            $this->attachments->store($user, $bill, $invoice);
        }

        return $bill;
    }

    public function approve(User $user, int $billId): Bill
    {
        $bill = $this->authorize($user, $billId);
        $bill->update([
            'status' => BillStatus::Approved->value,
            'approved_at' => now(),
        ]);

        return $bill->refresh();
    }

    public function markPaid(User $user, int $billId): Bill
    {
        $bill = $this->authorize($user, $billId);
        $bill->update([
            'status' => BillStatus::Paid->value,
            'paid_date' => now()->toDateString(),
            'paid_at' => now(),
        ]);

        return $bill->refresh();
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

        return VatCalculator::fromGross((float) ($data['gross_amount'] ?? $data['amount']), $vatRate);
    }

    private function authorize(User $user, int $billId): Bill
    {
        $bill = Bill::query()->findOrFail($billId);

        if ((int) $bill->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        return $bill;
    }
}
