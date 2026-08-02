<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Branch;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;

final readonly class OrganizationProvisionResult
{
    public function __construct(
        public Organization $organization,
        public User $owner,
        public Branch $branch,
        public Subscription $subscription,
    ) {}
}
