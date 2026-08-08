<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RotaVersion;
use App\Models\User;

final class RotaVersionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->organization_id !== null;
    }

    public function view(User $user, RotaVersion $version): bool
    {
        return $user->isAdmin()
            && (int) $version->organization_id === (int) $user->organization_id;
    }

    public function update(User $user, RotaVersion $version): bool
    {
        return $this->view($user, $version) && $version->status->isEditable();
    }

    public function publish(User $user, RotaVersion $version): bool
    {
        return $this->view($user, $version);
    }

    public function print(User $user, RotaVersion $version): bool
    {
        return $this->view($user, $version);
    }
}
