<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Models\CashDrawer;
use App\Models\User;
use Illuminate\Support\Collection;

final class CashDrawerService implements ServiceInterface
{
    public function __construct(private readonly BranchContext $branchContext) {}

    /**
     * @return Collection<int, CashDrawer>
     */
    public function list(User $user): Collection
    {
        return CashDrawer::query()
            ->with('branch')
            ->where('organization_id', $user->organization_id)
            ->when($this->branchContext->currentBranchId($user), fn ($q, $id) => $q->where('branch_id', $id))
            ->orderBy('name')
            ->get();
    }

    public function updateOpeningBalance(User $user, CashDrawer $drawer, float $balance): CashDrawer
    {
        if ((int) $drawer->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $drawer->update([
            'opening_balance' => $balance,
            'current_balance' => $balance,
        ]);

        return $drawer->refresh();
    }
}
