<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CrmCustomer;
use App\Models\User;

final class CrmCustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->organization_id !== null;
    }

    public function view(User $user, CrmCustomer $customer): bool
    {
        return $user->isAdmin()
            && (int) $customer->organization_id === (int) $user->organization_id;
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, CrmCustomer $customer): bool
    {
        return $this->view($user, $customer);
    }
}
