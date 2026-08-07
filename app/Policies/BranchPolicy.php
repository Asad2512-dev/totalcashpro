<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;

final class BranchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->organization_id !== null;
    }

    public function update(User $user, Branch $branch): bool
    {
        return $user->isAdmin()
            && (int) $branch->organization_id === (int) $user->organization_id;
    }
}
