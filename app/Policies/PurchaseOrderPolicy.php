<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

final class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && $user->organization_id !== null;
    }

    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->isAdmin()
            && (int) $purchaseOrder->organization_id === (int) $user->organization_id;
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $this->view($user, $purchaseOrder);
    }

    public function approve(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $this->view($user, $purchaseOrder);
    }

    public function receive(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $this->view($user, $purchaseOrder);
    }
}
