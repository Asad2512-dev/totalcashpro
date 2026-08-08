<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

final class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isBusinessAdmin();
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->isBusinessAdmin()
            && (int) $supplier->organization_id === (int) $user->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->isBusinessAdmin();
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $this->view($user, $supplier);
    }
}
