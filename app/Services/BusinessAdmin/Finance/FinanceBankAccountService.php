<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;
use App\Models\FinanceBankAccount;
use App\Models\User;
use App\Repositories\Contracts\FinanceBankAccountRepositoryInterface;
use App\Services\BusinessAdmin\BranchContext;

final class FinanceBankAccountService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly FinanceBankAccountRepositoryInterface $accounts,
    ) {}

    public function list(User $user): mixed
    {
        return $this->accounts->forOrganization(
            (int) $user->organization_id,
            $this->branchContext->currentBranchId($user),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(User $user, array $data): FinanceBankAccount
    {
        $branchId = $this->branchContext->currentBranchId($user);

        if (! empty($data['is_default'])) {
            FinanceBankAccount::query()
                ->where('organization_id', $user->organization_id)
                ->update(['is_default' => false]);
        }

        return $this->accounts->createAccount([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'name' => $data['name'],
            'bank_name' => $data['bank_name'] ?? null,
            'sort_code' => $data['sort_code'] ?? null,
            'account_number_last4' => isset($data['account_number_last4'])
                ? substr((string) $data['account_number_last4'], -4)
                : null,
            'currency' => $data['currency'] ?? 'GBP',
            'opening_balance' => $data['opening_balance'] ?? 0,
            'is_default' => (bool) ($data['is_default'] ?? false),
            'is_active' => true,
        ]);
    }
}
