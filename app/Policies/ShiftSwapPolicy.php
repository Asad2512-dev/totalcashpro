<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ShiftSwapRequest;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

final class ShiftSwapPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $user->organization_id !== null && ($user->isAdmin() || $user->isStaff());
    }

    public function view(User $user, ShiftSwapRequest $swap): bool
    {
        if (! $this->sameOrganization($user, $swap)) {
            return false;
        }

        return $user->isAdmin()
            || (int) $swap->requester_id === (int) $user->id
            || (int) $swap->target_user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isStaff() && $user->organization_id !== null;
    }

    public function review(User $user, ShiftSwapRequest $swap): bool
    {
        return $user->isAdmin() && $this->sameOrganization($user, $swap);
    }
}
