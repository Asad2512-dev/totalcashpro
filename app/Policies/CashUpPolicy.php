<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CashUp;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

final class CashUpPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function view(User $user, CashUp $cashUp): bool
    {
        return $this->isOrganizationAdmin($user) && $this->sameOrganization($user, $cashUp);
    }

    public function create(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function update(User $user, CashUp $cashUp): bool
    {
        return $this->view($user, $cashUp);
    }

    public function submit(User $user, CashUp $cashUp): bool
    {
        return $this->view($user, $cashUp);
    }

    public function approve(User $user, CashUp $cashUp): bool
    {
        return $this->view($user, $cashUp);
    }
}
