<?php

declare(strict_types=1);

namespace App\Services\Onboarding;

use App\Contracts\ServiceInterface;
use App\DTOs\OrganizationProvisionResult;
use App\DTOs\SignupData;
use App\Events\OrganizationRegistered;
use App\Models\User;

final class SignupService implements ServiceInterface
{
    public function __construct(
        private readonly OrganizationProvisioningService $provisioning,
    ) {}

    public function register(SignupData $data): OrganizationProvisionResult
    {
        $result = $this->provisioning->provisionFromSignup($data);

        event(new OrganizationRegistered($result));

        return $result;
    }

    public function ownerFromResult(OrganizationProvisionResult $result): User
    {
        return $result->owner;
    }
}
