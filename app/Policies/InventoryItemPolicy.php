<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

final class InventoryItemPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function view(User $user, InventoryItem $item): bool
    {
        return $this->isOrganizationAdmin($user) && $this->sameOrganization($user, $item);
    }

    public function create(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function update(User $user, InventoryItem $item): bool
    {
        return $this->view($user, $item);
    }
}
