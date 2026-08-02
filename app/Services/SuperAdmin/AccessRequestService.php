<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Concerns\LogsAdminActions;
use App\Contracts\ServiceInterface;
use App\Enums\AccessRequestStatus;
use App\Enums\OrganizationStatus;
use App\Enums\SubscriptionStatus;
use App\Models\AccessRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class AccessRequestService implements ServiceInterface
{
    use LogsAdminActions;

    public function __construct(
        private readonly OrganizationService $organizations,
    ) {}

    public function reject(AccessRequest $request, ?string $notes = null): AccessRequest
    {
        $old = $request->toArray();

        $request->update([
            'status' => AccessRequestStatus::Rejected,
            'admin_notes' => $notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $this->logAdminAction(
            'access_request.rejected',
            'Access request rejected: '.$request->business_name,
            $request,
            $old,
            $request->fresh()?->toArray(),
        );

        return $request->refresh();
    }

    /**
     * Approve request, create organization + one owner + trial subscription, email credentials.
     *
     * @return array{request: AccessRequest, organization: \App\Models\Organization, user: \App\Models\User, password: string}
     */
    public function approve(AccessRequest $request, ?string $notes = null): array
    {
        if ($request->status === AccessRequestStatus::Approved) {
            throw new RuntimeException('This access request is already approved.');
        }

        return DB::transaction(function () use ($request, $notes): array {
            $planSlug = $request->selected_plan instanceof \BackedEnum
                ? $request->selected_plan->value
                : (string) $request->selected_plan;

            $plan = Plan::query()->where('slug', $planSlug)->first()
                ?? Plan::query()->where('slug', 'basic')->firstOrFail();

            $result = $this->organizations->createWithOwner([
                'name' => $request->business_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => strlen((string) $request->country) === 2 ? strtoupper((string) $request->country) : 'GB',
                'currency' => 'GBP',
                'timezone' => 'Europe/London',
                'status' => OrganizationStatus::Trial->value,
                'trial_starts_at' => now(),
                'trial_ends_at' => now()->addDays(14),
                'owner_name' => $request->owner_name,
                'owner_email' => $request->email,
            ]);

            $organization = $result['organization'];
            $user = $result['owner'];
            $password = $result['password'];

            $subscription = Subscription::query()->create([
                'organization_id' => $organization->id,
                'plan_id' => $plan->id,
                'status' => SubscriptionStatus::Trialing,
                'starts_at' => now(),
                'trial_starts_at' => now(),
                'trial_ends_at' => now()->addDays(14),
                'current_period_start' => now(),
                'current_period_end' => now()->addDays(14),
            ]);

            SubscriptionHistory::query()->create([
                'subscription_id' => $subscription->id,
                'organization_id' => $organization->id,
                'plan_id' => $plan->id,
                'event' => 'trial.started',
                'meta' => ['source' => 'access_request', 'access_request_id' => $request->id],
                'created_at' => now(),
            ]);

            $request->update([
                'status' => AccessRequestStatus::Approved,
                'organization_id' => $organization->id,
                'admin_notes' => $notes,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            $this->logAdminAction(
                'access_request.approved',
                'Access approved and organisation created: '.$organization->name,
                $organization,
                null,
                [
                    'access_request_id' => $request->id,
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                    'plan' => $plan->slug,
                ],
            );

            return [
                'request' => $request->refresh(),
                'organization' => $organization->refresh(),
                'user' => $user,
                'password' => $password,
            ];
        });
    }
}
