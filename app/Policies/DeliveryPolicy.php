<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

final class DeliveryPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $this->isOrganizationAdmin($user) || $user->isRider();
    }

    public function view(User $user, Delivery $delivery): bool
    {
        if (! $this->sameOrganization($user, $delivery)) {
            return false;
        }

        return $user->isAdmin() || ((int) $delivery->rider_id === (int) $user->id);
    }

    public function update(User $user, Delivery $delivery): bool
    {
        return $this->view($user, $delivery);
    }
}
