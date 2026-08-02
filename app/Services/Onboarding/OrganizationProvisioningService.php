<?php

declare(strict_types=1);

namespace App\Services\Onboarding;

use App\Contracts\ServiceInterface;
use App\DTOs\OrganizationProvisionResult;
use App\DTOs\SignupData;
use App\Enums\OrganizationStatus;
use App\Enums\RoleSlug;
use App\Enums\SubscriptionStatus;
use App\Models\Branch;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class OrganizationProvisioningService implements ServiceInterface
{
    public const TRIAL_DAYS = 14;

    public const DEFAULT_BRANCH_NAME = 'Main Branch';

    /**
     * Self-service signup: organisation, owner, branch, and Professional trial in one transaction.
     */
    public function provisionFromSignup(SignupData $data): OrganizationProvisionResult
    {
        if (User::query()->where('email', $data->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'An account with this email already exists.',
            ]);
        }

        $professionalPlan = Plan::query()->where('slug', 'professional')->firstOrFail();
        $adminRole = Role::query()->where('slug', RoleSlug::Admin->value)->firstOrFail();

        $trialStarts = now();
        $trialEnds = now()->addDays(self::TRIAL_DAYS);

        return DB::transaction(function () use ($data, $professionalPlan, $adminRole, $trialStarts, $trialEnds): OrganizationProvisionResult {
            $slug = $this->uniqueOrganizationSlug($data->businessName);

            $organization = Organization::query()->create([
                'name' => $data->businessName,
                'slug' => $slug,
                'email' => $data->email,
                'phone' => $data->phone,
                'country' => $data->country,
                'currency' => 'GBP',
                'timezone' => 'Europe/London',
                'business_type' => $data->businessType->value,
                'status' => OrganizationStatus::Trial,
                'trial_starts_at' => $trialStarts,
                'trial_ends_at' => $trialEnds,
                'settings' => [
                    'strict_rota_clockin' => '0',
                    'strict_business_hours' => '0',
                    'signup_source' => 'self_service',
                ],
            ]);

            $owner = User::query()->create([
                'name' => $data->ownerName,
                'email' => $data->email,
                'phone' => $data->phone,
                'password' => Hash::make($data->password),
                'role_id' => $adminRole->id,
                'organization_id' => $organization->id,
                'status' => 'active',
                'email_verified_at' => null,
                'onboarding_completed_at' => null,
            ]);

            $organization->update(['owner_user_id' => $owner->id]);

            $branch = Branch::query()->create([
                'organization_id' => $organization->id,
                'name' => self::DEFAULT_BRANCH_NAME,
                'slug' => 'main-branch',
                'status' => 'open',
            ]);

            $owner->update(['branch_id' => $branch->id]);

            $subscription = Subscription::query()->create([
                'organization_id' => $organization->id,
                'plan_id' => $professionalPlan->id,
                'status' => SubscriptionStatus::Trialing,
                'starts_at' => $trialStarts,
                'trial_starts_at' => $trialStarts,
                'trial_ends_at' => $trialEnds,
                'current_period_start' => $trialStarts,
                'current_period_end' => $trialEnds,
            ]);

            SubscriptionHistory::query()->create([
                'subscription_id' => $subscription->id,
                'organization_id' => $organization->id,
                'plan_id' => $professionalPlan->id,
                'event' => 'trial.started',
                'meta' => [
                    'source' => 'self_service_signup',
                    'trial_days' => self::TRIAL_DAYS,
                    'plan_slug' => $professionalPlan->slug,
                ],
                'created_at' => now(),
            ]);

            return new OrganizationProvisionResult(
                organization: $organization->refresh(),
                owner: $owner->refresh(),
                branch: $branch,
                subscription: $subscription->refresh(),
            );
        });
    }

    private function uniqueOrganizationSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'business';
        $slug = $base;
        $i = 1;

        while (Organization::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
