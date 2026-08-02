<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrganizationRegistered;
use App\Services\Logging\ActivityLogger;
use App\Services\Logging\AuditLogger;
use App\Services\Onboarding\OrganizationProvisioningService;

final class LogOrganizationRegistered
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function handle(OrganizationRegistered $event): void
    {
        $organization = $event->result->organization;
        $owner = $event->result->owner;

        $this->activityLogger->log(
            event: 'organization.registered',
            description: 'New business registered: '.$organization->name,
            actor: $owner,
            subject: $organization,
            properties: [
                'organization_id' => $organization->id,
                'owner_id' => $owner->id,
                'plan' => 'professional',
                'trial_days' => OrganizationProvisioningService::TRIAL_DAYS,
            ],
        );

        $this->auditLogger->log(
            action: 'signup.completed',
            user: $owner,
            target: $organization,
            newValues: [
                'organization_id' => $organization->id,
                'subscription_id' => $event->result->subscription->id,
            ],
        );
    }
}
