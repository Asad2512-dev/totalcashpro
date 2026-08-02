<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Contracts\ServiceInterface;
use App\Enums\SubscriptionStatus;
use App\Models\Organization;
use App\Models\Subscription;
use Carbon\CarbonInterface;

final class TrialService implements ServiceInterface
{
    public function subscription(Organization $organization): ?Subscription
    {
        return $organization->currentSubscription;
    }

    public function isOnTrial(Organization $organization): bool
    {
        $subscription = $this->subscription($organization);

        if ($subscription === null) {
            return false;
        }

        return $subscription->status === SubscriptionStatus::Trialing
            && $this->trialEndsAt($subscription)?->isFuture();
    }

    public function trialExpired(Organization $organization): bool
    {
        $subscription = $this->subscription($organization);

        if ($subscription === null) {
            return false;
        }

        if ($subscription->status === SubscriptionStatus::Expired) {
            return true;
        }

        if ($subscription->status !== SubscriptionStatus::Trialing) {
            return false;
        }

        $ends = $this->trialEndsAt($subscription);

        return $ends !== null && $ends->isPast();
    }

    public function daysRemaining(Organization $organization): ?int
    {
        $subscription = $this->subscription($organization);
        $ends = $subscription ? $this->trialEndsAt($subscription) : null;

        if ($ends === null || $ends->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($ends, false) + 1;
    }

    /**
     * @return array{status: string, days_remaining: int|null, trial_ends_at: CarbonInterface|null, on_trial: bool}
     */
    public function summary(Organization $organization): array
    {
        $subscription = $this->subscription($organization);
        $ends = $subscription ? $this->trialEndsAt($subscription) : null;

        return [
            'status' => $subscription?->status?->value ?? 'none',
            'days_remaining' => $this->daysRemaining($organization),
            'trial_ends_at' => $ends,
            'on_trial' => $this->isOnTrial($organization),
        ];
    }

    private function trialEndsAt(Subscription $subscription): ?CarbonInterface
    {
        return $subscription->trial_ends_at ?? $subscription->organization?->trial_ends_at;
    }
}
