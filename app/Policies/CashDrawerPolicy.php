<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CashDrawer;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

final class CashDrawerPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->organization_id !== null;
    }

    public function view(User $user, CashDrawer $drawer): bool
    {
        return $this->sameOrganization($user, $drawer);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() && $user->organization_id !== null;
    }

    public function update(User $user, CashDrawer $drawer): bool
    {
        return $this->sameOrganization($user, $drawer);
    }

    public function transfer(User $user, CashDrawer $drawer): bool
    {
        return $this->sameOrganization($user, $drawer) && $user->isAdmin();
    }

    public function adjustOpeningFloat(User $user, CashDrawer $drawer): bool
    {
        return $this->sameOrganization($user, $drawer) && $user->isAdmin();
    }
}
