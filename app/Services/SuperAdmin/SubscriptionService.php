<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Concerns\LogsAdminActions;
use App\Contracts\ServiceInterface;
use App\Enums\OrganizationStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;

final class SubscriptionService implements ServiceInterface
{
    use LogsAdminActions;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Subscription
    {
        $subscription = Subscription::query()->create($data);
        $this->history($subscription, 'subscription.created');
        $this->logAdminAction('subscription.created', 'Subscription created', $subscription, null, $data);

        return $subscription;
    }

    public function changePlan(Subscription $subscription, Plan $plan): Subscription
    {
        $old = ['plan_id' => $subscription->plan_id];
        $subscription->update(['plan_id' => $plan->id]);
        $this->history($subscription, 'plan.changed', ['from' => $old['plan_id'], 'to' => $plan->id]);
        $this->logAdminAction('subscription.plan_changed', 'Plan changed to '.$plan->name, $subscription, $old, ['plan_id' => $plan->id]);

        return $subscription->refresh();
    }

    public function setStatus(Subscription $subscription, SubscriptionStatus $status): Subscription
    {
        $old = ['status' => $subscription->status?->value];
        $payload = ['status' => $status];

        if ($status === SubscriptionStatus::Cancelled) {
            $payload['cancelled_at'] = now();
        }

        if ($status === SubscriptionStatus::Active) {
            $payload['cancelled_at'] = null;
            $payload['current_period_start'] = now();
            $payload['current_period_end'] = now()->addMonth();
            Organization::query()->whereKey($subscription->organization_id)
                ->update(['status' => OrganizationStatus::Active]);
        }

        $subscription->update($payload);
        $this->history($subscription, 'status.'.$status->value);
        $this->logAdminAction('subscription.status_changed', 'Subscription marked '.$status->label(), $subscription, $old, $payload);

        return $subscription->refresh();
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function history(Subscription $subscription, string $event, array $meta = []): void
    {
        SubscriptionHistory::query()->create([
            'subscription_id' => $subscription->id,
            'organization_id' => $subscription->organization_id,
            'plan_id' => $subscription->plan_id,
            'event' => $event,
            'meta' => $meta ?: null,
            'created_at' => now(),
        ]);
    }
}
