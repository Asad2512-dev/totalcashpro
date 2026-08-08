<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ScheduledReport;
use App\Models\User;
use App\Policies\Concerns\ChecksOrganizationAccess;

final class ScheduledReportPolicy
{
    use ChecksOrganizationAccess;

    public function viewAny(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function view(User $user, ScheduledReport $report): bool
    {
        return $this->isOrganizationAdmin($user) && $this->sameOrganization($user, $report);
    }

    public function create(User $user): bool
    {
        return $this->isOrganizationAdmin($user);
    }

    public function update(User $user, ScheduledReport $report): bool
    {
        return $this->view($user, $report);
    }
}
