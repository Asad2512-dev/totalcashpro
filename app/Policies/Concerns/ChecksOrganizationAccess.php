<?php

declare(strict_types=1);

namespace App\Policies\Concerns;

use App\Models\User;

trait ChecksOrganizationAccess
{
    protected function sameOrganization(User $user, object $model): bool
    {
        return $user->organization_id !== null
            && isset($model->organization_id)
            && (int) $model->organization_id === (int) $user->organization_id;
    }

    protected function isOrganizationAdmin(User $user): bool
    {
        return $user->isAdmin() && $user->organization_id !== null;
    }
}
