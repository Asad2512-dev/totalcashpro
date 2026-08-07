<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;
use App\Enums\FinanceIncomeSource;
use App\Enums\FinanceStatus;
use App\Models\FinanceIncomeEntry;
use App\Models\User;
use App\Repositories\Contracts\FinanceBankAccountRepositoryInterface;
use App\Repositories\Contracts\FinanceIncomeRepositoryInterface;
use App\Services\BusinessAdmin\BranchContext;
use App\Support\Finance\VatCalculator;
use Illuminate\Http\UploadedFile;

final class FinanceIncomeService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly FinanceIncomeRepositoryInterface $income,
        private readonly FinanceBankAccountRepositoryInterface $bankAccounts,
        private readonly FinanceAttachmentService $attachments,
    ) {}

    public function list(User $user, ?string $status = null): mixed
    {
        $filters = $status ? ['status' => $status] : [];

        return $this->income->paginateForBranch(
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
    public function store(User $user, array $data, ?UploadedFile $receipt = null): FinanceIncomeEntry
    {
        $branchId = $this->branchContext->requireBranchId($user);
        $amounts = $this->resolveAmounts($data);

        $entry = $this->income->createEntry([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'source' => FinanceIncomeSource::Manual->value,
            'title' => $data['title'],
            'net_amount' => $amounts['net'],
            'vat_amount' => $amounts['vat'],
            'gross_amount' => $amounts['gross'],
            'income_date' => $data['income_date'],
            'status' => FinanceStatus::Draft->value,
            'notes' => $data['notes'] ?? null,
            'created_by' => $user->id,
        ]);

        if ($receipt !== null) {
            $this->attachments->store($user, $entry, $receipt);
        }

        return $entry;
    }

    public function approve(User $user, int $entryId): FinanceIncomeEntry
    {
        $entry = $this->authorize($user, $entryId);
        $entry->update([
            'status' => FinanceStatus::Approved->value,
            'approved_at' => now(),
        ]);

        return $entry->refresh();
    }

    public function markPaid(User $user, int $entryId): FinanceIncomeEntry
    {
        $entry = $this->authorize($user, $entryId);
        $entry->update([
            'status' => FinanceStatus::Paid->value,
            'paid_at' => now(),
        ]);

        return $entry->refresh();
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

    private function authorize(User $user, int $entryId): FinanceIncomeEntry
    {
        $entry = FinanceIncomeEntry::query()->findOrFail($entryId);

        if ((int) $entry->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        return $entry;
    }
}
