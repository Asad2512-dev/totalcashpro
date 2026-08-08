<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GoodsReceivedNote;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

final class GoodsReceivedNotePolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function view(User $user, GoodsReceivedNote $note): bool
    {
        return $this->isOrganizationAdmin($user) && $this->sameOrganization($user, $note);
    }

    public function create(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }
}
