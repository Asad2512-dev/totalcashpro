<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BusinessAlert;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

final class BusinessAlertPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function view(User $user, BusinessAlert $alert): bool
    {
        return $this->isOrganizationAdmin($user) && $this->sameOrganization($user, $alert);
    }

    public function update(User $user, BusinessAlert $alert): bool
    {
        return $this->view($user, $alert);
    }
}
