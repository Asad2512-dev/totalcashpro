<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

final class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->organization_id !== null && ($user->isAdmin() || $user->isStaff());
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        if ((int) $leaveRequest->organization_id !== (int) $user->organization_id) {
            return false;
        }

        return $user->isAdmin() || (int) $leaveRequest->user_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isStaff() && $user->organization_id !== null;
    }

    public function review(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->isAdmin()
            && (int) $leaveRequest->organization_id === (int) $user->organization_id;
    }
}
