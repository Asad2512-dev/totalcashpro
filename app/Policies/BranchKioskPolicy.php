<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BranchKiosk;
use App\Models\User;

final class BranchKioskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->organization_id !== null;
    }

    public function view(User $user, BranchKiosk $kiosk): bool
    {
        return $user->isAdmin()
            && (int) $kiosk->organization_id === (int) $user->organization_id;
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, BranchKiosk $kiosk): bool
    {
        return $this->view($user, $kiosk);
    }

    public function delete(User $user, BranchKiosk $kiosk): bool
    {
        return $this->view($user, $kiosk);
    }
}
