<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\CashDrawerSessionStatus;
use App\Models\CashDrawer;
use App\Models\CashDrawerSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CashDrawerSessionService implements ServiceInterface
{
    public function __construct(
        private readonly BranchContext $branchContext,
        private readonly CashCountingService $counting,
    ) {}

  /**
   * @param  list<array{coin?: string, note?: string, qty: int, value: float}>|null  $openingCount
   */
    public function open(
        User $user,
        CashDrawer $drawer,
        ?float $openingFloat = null,
        ?array $openingCount = null,
        ?string $notes = null,
    ): CashDrawerSession {
        $this->assertDrawerAccess($user, $drawer);

        if ($drawer->openSession()->exists()) {
            throw ValidationException::withMessages([
                'drawer' => 'This till already has an open session.',
            ]);
        }

        $float = $openingFloat ?? $drawer->defaultOpeningFloat();
        $countedFloat = $openingCount !== null
            ? $this->counting->sumDenominationRows($openingCount)
            : $float;

        return DB::transaction(function () use ($user, $drawer, $float, $countedFloat, $openingCount, $notes): CashDrawerSession {
            $session = CashDrawerSession::query()->create([
                'organization_id' => $drawer->organization_id,
                'branch_id' => $drawer->branch_id,
                'cash_drawer_id' => $drawer->id,
                'opened_by_user_id' => $user->id,
                'opened_at' => now(),
                'opening_float' => $countedFloat > 0 ? $countedFloat : $float,
                'opening_count' => $openingCount,
                'status' => CashDrawerSessionStatus::Open->value,
                'notes' => $notes,
            ]);

            $drawer->update([
                'current_balance' => $session->opening_float,
                'last_opened_at' => now(),
            ]);

            return $session->load(['cashDrawer', 'openedBy']);
        });
    }

  /**
   * @param  list<array{coin?: string, note?: string, qty: int, value: float}>|null  $closingCount
   */
    public function close(
        User $user,
        CashDrawerSession $session,
        float $actualCash,
        float $expectedCash,
        ?array $closingCount = null,
        ?string $varianceReason = null,
    ): CashDrawerSession {
        $this->assertSessionAccess($user, $session);

        if (! $session->isOpen()) {
            throw ValidationException::withMessages([
                'session' => 'This session is not open.',
            ]);
        }

        $variance = round($actualCash - $expectedCash, 2);

        return DB::transaction(function () use ($user, $session, $actualCash, $expectedCash, $variance, $closingCount, $varianceReason): CashDrawerSession {
            $session->update([
                'closed_by_user_id' => $user->id,
                'closed_at' => now(),
                'closing_count' => $closingCount,
                'actual_cash' => $actualCash,
                'expected_cash' => $expectedCash,
                'variance' => $variance,
                'variance_reason' => $varianceReason,
                'status' => CashDrawerSessionStatus::Closed->value,
            ]);

            $session->cashDrawer->update([
                'current_balance' => $actualCash,
                'last_closed_at' => now(),
            ]);

            return $session->refresh();
        });
    }

    public function findOpenForDrawer(CashDrawer $drawer): ?CashDrawerSession
    {
        return $drawer->openSession;
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

    private function assertSessionAccess(User $user, CashDrawerSession $session): void
    {
        if ((int) $session->organization_id !== (int) $user->organization_id) {
            abort(403);
        }
    }
}
