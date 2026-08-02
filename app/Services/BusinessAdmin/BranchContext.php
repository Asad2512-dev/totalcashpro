<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 * Session-backed branch filter for Business Admin.
 * null branch id means "all branches" (combined view).
 */
final class BranchContext implements ServiceInterface
{
    private const SESSION_KEY = 'business_admin.branch_id';

    public function currentBranchId(?User $user = null): ?int
    {
        $user ??= auth()->user();
        if ($user === null || $user->organization_id === null) {
            return null;
        }

        $raw = Session::get(self::SESSION_KEY);

        if ($raw === null || $raw === '' || $raw === 'all') {
            return null;
        }

        $branchId = (int) $raw;

        if (! $this->belongsToOrganization($branchId, (int) $user->organization_id)) {
            Session::forget(self::SESSION_KEY);

            return null;
        }

        return $branchId;
    }

    public function setBranchId(?int $branchId, ?User $user = null): void
    {
        $user ??= auth()->user();

        if ($branchId === null) {
            Session::put(self::SESSION_KEY, 'all');

            return;
        }

        if ($user !== null && ! $this->belongsToOrganization($branchId, (int) $user->organization_id)) {
            abort(403, 'Branch does not belong to your organisation.');
        }

        Session::put(self::SESSION_KEY, $branchId);
    }

    /**
     * @return Collection<int, Branch>
     */
    public function resolveBranches(User $user): Collection
    {
        return Branch::query()
            ->where('organization_id', $user->organization_id)
            ->orderBy('name')
            ->get();
    }

    public function requireBranchId(User $user): int
    {
        $branchId = $this->currentBranchId($user);

        if ($branchId !== null) {
            return $branchId;
        }

        // Staff (and assigned users) always operate in their home branch.
        if ($user->branch_id) {
            $this->ensureBranchBelongsToOrg((int) $user->branch_id, $user);

            return (int) $user->branch_id;
        }

        $first = $this->resolveBranches($user)->first();

        if ($first === null) {
            abort(422, 'Create a branch before continuing.');
        }

        $this->setBranchId((int) $first->id, $user);

        return (int) $first->id;
    }

    public function belongsToOrganization(int $branchId, int $organizationId): bool
    {
        return Branch::query()
            ->whereKey($branchId)
            ->where('organization_id', $organizationId)
            ->exists();
    }

    public function ensureBranchBelongsToOrg(int $branchId, User $user): void
    {
        if (! $this->belongsToOrganization($branchId, (int) $user->organization_id)) {
            abort(403, 'Branch does not belong to your organisation.');
        }
    }
}
