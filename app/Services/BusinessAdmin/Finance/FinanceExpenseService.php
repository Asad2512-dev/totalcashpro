<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;
use App\Enums\FinanceStatus;
use App\Models\Spending;
use App\Models\User;
use App\Repositories\Contracts\FinanceBankAccountRepositoryInterface;
use App\Repositories\Contracts\SpendingRepositoryInterface;
use App\Services\BusinessAdmin\BranchContext;
use App\Support\Finance\VatCalculator;
use Illuminate\Http\UploadedFile;

final class FinanceExpenseService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly SpendingRepositoryInterface $expenses,
        private readonly FinanceBankAccountRepositoryInterface $bankAccounts,
        private readonly FinanceAttachmentService $attachments,
    ) {}

    public function list(User $user, ?string $status = null): mixed
    {
        $filters = $status ? ['status' => $status] : [];

        return $this->expenses->paginateForBranch(
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
    public function store(User $user, array $data, ?UploadedFile $receipt = null): Spending
    {
        $branchId = $this->branchContext->requireBranchId($user);
        $amounts = $this->resolveAmounts($data);

        $expense = $this->expenses->createExpense([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'title' => $data['title'],
            'category' => $data['category'] ?? 'other',
            'amount' => $amounts['gross'],
            'net_amount' => $amounts['net'],
            'vat_amount' => $amounts['vat'],
            'gross_amount' => $amounts['gross'],
            'status' => FinanceStatus::Draft->value,
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'spent_date' => $data['spent_date'],
            'payment_method' => $data['payment_method'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $user->id,
        ]);

        if ($receipt !== null) {
            $this->attachments->store($user, $expense, $receipt);
        }

        return $expense;
    }

    public function approve(User $user, int $expenseId): Spending
    {
        $expense = $this->authorize($user, $expenseId);
        $expense->update([
            'status' => FinanceStatus::Approved->value,
            'approved_at' => now(),
        ]);

        return $expense->refresh();
    }

    public function markPaid(User $user, int $expenseId): Spending
    {
        $expense = $this->authorize($user, $expenseId);
        $expense->update([
            'status' => FinanceStatus::Paid->value,
            'paid_at' => now(),
        ]);

        return $expense->refresh();
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

    private function authorize(User $user, int $expenseId): Spending
    {
        $expense = Spending::query()->findOrFail($expenseId);

        if ((int) $expense->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        return $expense;
    }
}
