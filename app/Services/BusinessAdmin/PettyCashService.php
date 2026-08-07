<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Models\PettyCashAccount;
use App\Models\PettyCashTransaction;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class PettyCashService implements ServiceInterface
{
    public function __construct(private readonly BranchContext $branchContext) {}

    /**
     * @return Collection<int, PettyCashAccount>
     */
    public function listAccounts(User $user): Collection
    {
        return PettyCashAccount::query()
            ->where('organization_id', $user->organization_id)
            ->when($this->branchContext->currentBranchId($user), fn ($q, $id) => $q->where('branch_id', $id))
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function storeAccount(User $user, array $data): PettyCashAccount
    {
        $opening = (float) ($data['opening_balance'] ?? 0);
        $branchId = $this->branchContext->currentBranchId($user)
            ?? $user->branch_id
            ?? $user->organization?->branches()->orderBy('id')->value('id');

        return PettyCashAccount::query()->create([
            'organization_id' => $user->organization_id,
            'branch_id' => $branchId,
            'name' => $data['name'],
            'float_amount' => (float) ($data['float_amount'] ?? $opening),
            'opening_balance' => $opening,
            'balance' => $opening,
            'custodian_user_id' => $data['custodian_user_id'] ?? null,
            'is_active' => true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function recordTransaction(User $user, PettyCashAccount $account, array $data): PettyCashTransaction
    {
        $this->assertSameOrg($user, $account);

        return DB::transaction(function () use ($user, $account, $data): PettyCashTransaction {
            $amount = (float) $data['amount'];
            $type = (string) $data['type'];
            $signed = $type === 'expense' ? -abs($amount) : abs($amount);

            $transaction = PettyCashTransaction::query()->create([
                'petty_cash_account_id' => $account->id,
                'organization_id' => $user->organization_id,
                'branch_id' => $account->branch_id,
                'type' => $type,
                'amount' => abs($amount),
                'description' => $data['description'] ?? null,
                'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
                'created_by' => $user->id,
            ]);

            $account->update(['balance' => (float) $account->balance + $signed]);

            return $transaction;
        });
    }

    private function assertSameOrg(User $user, PettyCashAccount $account): void
    {
        if ((int) $account->organization_id !== (int) $user->organization_id) {
            abort(403);
        }
    }
}
