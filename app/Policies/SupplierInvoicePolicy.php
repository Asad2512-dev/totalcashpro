<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SupplierInvoice;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

final class SupplierInvoicePolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function view(User $user, SupplierInvoice $invoice): bool
    {
        return $this->isOrganizationAdmin($user) && $this->sameOrganization($user, $invoice);
    }

    public function update(User $user, SupplierInvoice $invoice): bool
    {
        return $this->view($user, $invoice);
    }
}
