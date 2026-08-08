<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\CashDrawerMovementType;
use App\Models\CashDrawer;
use App\Models\CashDrawerSession;
use App\Models\CashDrawerTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CashMovementService implements ServiceInterface
{
    public function __construct(private readonly BranchContext $branchContext) {}

    public function record(
        User $user,
        CashDrawer $drawer,
        CashDrawerMovementType $type,
        float $amount,
        string $description,
        ?CashDrawerSession $session = null,
        ?string $reason = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): CashDrawerTransaction {
        $this->assertDrawerAccess($user, $drawer);

        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'Amount must be greater than zero.']);
        }

        $session ??= $drawer->openSession;

        return DB::transaction(function () use ($user, $drawer, $type, $amount, $description, $session, $reason, $referenceType, $referenceId): CashDrawerTransaction {
            $transaction = CashDrawerTransaction::query()->create([
                'cash_drawer_id' => $drawer->id,
                'cash_drawer_session_id' => $session?->id,
                'organization_id' => $drawer->organization_id,
                'branch_id' => $drawer->branch_id,
                'type' => $type->value,
                'amount' => $amount,
                'description' => $description,
                'reason' => $reason,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'created_by' => $user->id,
            ]);

            $drawer->update([
                'current_balance' => round((float) $drawer->current_balance + $transaction->signedAmount(), 2),
            ]);

            return $transaction;
        });
    }

    public function transfer(
        User $user,
        CashDrawer $from,
        CashDrawer $to,
        float $amount,
        string $reason,
    ): array {
        $this->assertDrawerAccess($user, $from);
        $this->assertDrawerAccess($user, $to);

        if ((int) $from->branch_id !== (int) $to->branch_id) {
            throw ValidationException::withMessages([
                'transfer' => 'Transfers must stay within the same branch.',
            ]);
        }

        return DB::transaction(function () use ($user, $from, $to, $amount, $reason): array {
            $out = $this->record(
                $user,
                $from,
                CashDrawerMovementType::Transfer,
                $amount,
                sprintf('Transfer to %s', $to->name),
                $from->openSession,
                $reason,
            );

            $in = $this->record(
                $user,
                $to,
                CashDrawerMovementType::Deposit,
                $amount,
                sprintf('Transfer from %s', $from->name),
                $to->openSession,
                $reason,
            );

            $out->update(['paired_transaction_id' => $in->id, 'transfer_drawer_id' => $to->id]);
            $in->update(['paired_transaction_id' => $out->id, 'transfer_drawer_id' => $from->id]);

            return ['out' => $out->refresh(), 'in' => $in->refresh()];
        });
    }

    private function assertDrawerAccess(User $user, CashDrawer $drawer): void
    {
        if ((int) $drawer->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $branchId = $this->branchContext->currentBranchId($user);
        if ($branchId !== null && (int) $drawer->branch_id !== $branchId) {
            abort(403);
        }
    }
}
