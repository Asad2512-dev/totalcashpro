<?php

declare(strict_types=1);

namespace App\Events;

use App\DTOs\OrganizationProvisionResult;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class OrganizationRegistered
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public OrganizationProvisionResult $result,
    ) {}
}
