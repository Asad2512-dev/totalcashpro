<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class OwnerCredentialsSent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public User $owner,
        public Organization $organization,
        public string $password,
    ) {}
}
