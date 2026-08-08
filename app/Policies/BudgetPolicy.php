<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

final class BudgetPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function view(User $user, Budget $budget): bool
    {
        return $this->isOrganizationAdmin($user) && $this->sameOrganization($user, $budget);
    }

    public function create(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function update(User $user, Budget $budget): bool
    {
        return $this->view($user, $budget);
    }
}
