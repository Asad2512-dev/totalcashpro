<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Wage;
use App\Policies\Concerns\ChecksOrganizationAccess;

final class WagePolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function view(User $user, Wage $wage): bool
    {
        return $this->isOrganizationAdmin($user) && $this->sameOrganization($user, $wage);
    }

    public function update(User $user, Wage $wage): bool
    {
        return $this->view($user, $wage);
    }

    public function approve(User $user, Wage $wage): bool
    {
        return $this->view($user, $wage);
    }
}
