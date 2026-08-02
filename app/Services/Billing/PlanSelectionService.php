<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Contracts\ServiceInterface;
use App\Enums\OrganizationStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\User;
use App\Services\Logging\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Post-trial plan selection — stores plan choice and prepares for Stripe billing later.
 */
final class PlanSelectionService implements ServiceInterface
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
        private readonly TrialService $trials,
    ) {}

    /**
     * @return list<Plan>
     */
    public function selectablePlans(): array
    {
        return Plan::query()
            ->where('is_public', true)
            ->whereIn('slug', ['basic', 'professional'])
            ->orderBy('sort_order')
            ->get()
            ->all();
    }

    public function selectPlan(Organization $organization, User $actor, string $planSlug): Subscription
    {
        $plan = Plan::query()->where('slug', $planSlug)->where('is_public', true)->first();

        if ($plan === null) {
            throw ValidationException::withMessages([
                'plan' => 'Please choose a valid plan.',
            ]);
        }

        $subscription = $organization->currentSubscription;

        if ($subscription === null) {
            throw ValidationException::withMessages([
                'plan' => 'No subscription found for this business.',
            ]);
        }

        return DB::transaction(function () use ($organization, $actor, $plan, $subscription): Subscription {
            $periodStart = now();
            $periodEnd = now()->addMonth();

            $subscription->update([
                'plan_id' => $plan->id,
                'pending_plan_id' => null,
                'status' => SubscriptionStatus::Active,
                'trial_ends_at' => $subscription->trial_ends_at ?? now(),
                'current_period_start' => $periodStart,
                'current_period_end' => $periodEnd,
                'starts_at' => $subscription->starts_at ?? $periodStart,
            ]);

            $organization->update([
                'status' => OrganizationStatus::Active,
            ]);

            SubscriptionHistory::query()->create([
                'subscription_id' => $subscription->id,
                'organization_id' => $organization->id,
                'plan_id' => $plan->id,
                'event' => 'plan.selected',
                'meta' => [
                    'source' => 'post_trial_selection',
                    'billing_provider' => 'manual',
                    'awaiting_stripe' => true,
                    'selected_by' => $actor->id,
                ],
                'created_at' => now(),
            ]);

            $this->activityLogger->log(
                event: 'subscription.plan_selected',
                description: $organization->name.' selected '.$plan->name.' plan',
                actor: $actor,
                subject: $subscription,
                properties: ['plan_slug' => $plan->slug],
            );

            return $subscription->refresh();
        });
    }

    public function requiresPlanSelection(Organization $organization): bool
    {
        return $this->trials->trialExpired($organization)
            && $organization->currentSubscription?->status !== SubscriptionStatus::Active;
    }
}
