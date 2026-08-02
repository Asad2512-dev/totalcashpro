<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Contracts\ServiceInterface;
use App\Enums\OrganizationStatus;
use App\Enums\PlanFeature;
use App\Enums\SubscriptionStatus;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

/**
 * Centralized subscription entitlement checks. Never hardcode plan names in controllers.
 */
final class FeatureAccessService implements ServiceInterface
{
    private const CACHE_TTL_SECONDS = 300;

    /**
     * Default entitlements when a plan has no structured keys (legacy bullet-only plans).
     *
     * @return array{max_branches: int|null, max_staff: int|null, features: array<string, bool>}
     */
    public function defaultsForSlug(string $slug): array
    {
        return match ($slug) {
            'basic' => [
                'max_branches' => 1,
                'max_staff' => 5,
                'features' => [
                    PlanFeature::CashUp->value => true,
                    PlanFeature::Attendance->value => true,
                    PlanFeature::Reports->value => true,
                    PlanFeature::Inventory->value => false,
                    PlanFeature::Payroll->value => false,
                    PlanFeature::Rota->value => false,
                    PlanFeature::Suppliers->value => false,
                    PlanFeature::AdvancedReports->value => false,
                    PlanFeature::MultipleBranches->value => false,
                    PlanFeature::StaffPanel->value => true,
                ],
            ],
            'professional' => [
                'max_branches' => null,
                'max_staff' => null,
                'features' => [
                    PlanFeature::CashUp->value => true,
                    PlanFeature::Attendance->value => true,
                    PlanFeature::Reports->value => true,
                    PlanFeature::Inventory->value => true,
                    PlanFeature::Payroll->value => true,
                    PlanFeature::Rota->value => true,
                    PlanFeature::Suppliers->value => true,
                    PlanFeature::AdvancedReports->value => true,
                    PlanFeature::MultipleBranches->value => true,
                    PlanFeature::StaffPanel->value => true,
                ],
            ],
            'enterprise' => [
                'max_branches' => null,
                'max_staff' => null,
                'features' => collect(PlanFeature::moduleFeatures())
                    ->mapWithKeys(fn (PlanFeature $f) => [$f->value => true])
                    ->all(),
            ],
            default => [
                'max_branches' => 1,
                'max_staff' => 5,
                'features' => [
                    PlanFeature::CashUp->value => true,
                    PlanFeature::Attendance->value => true,
                    PlanFeature::Reports->value => true,
                    PlanFeature::Inventory->value => false,
                    PlanFeature::Payroll->value => false,
                    PlanFeature::Rota->value => false,
                    PlanFeature::Suppliers->value => false,
                    PlanFeature::AdvancedReports->value => false,
                    PlanFeature::MultipleBranches->value => false,
                    PlanFeature::StaffPanel->value => true,
                ],
            ],
        };
    }

    public function organizationIsAccessible(?Organization $organization): bool
    {
        if ($organization === null) {
            return false;
        }

        $status = $organization->status instanceof OrganizationStatus
            ? $organization->status
            : OrganizationStatus::tryFrom((string) $organization->status);

        return in_array($status, [OrganizationStatus::Active, OrganizationStatus::Trial], true);
    }

    public function subscriptionIsUsable(?Subscription $subscription): bool
    {
        if ($subscription === null) {
            return false;
        }

        $status = $subscription->status instanceof SubscriptionStatus
            ? $subscription->status
            : SubscriptionStatus::tryFrom((string) $subscription->status);

        return in_array($status, [
            SubscriptionStatus::Active,
            SubscriptionStatus::Trialing,
            SubscriptionStatus::Free,
            SubscriptionStatus::Lifetime,
            SubscriptionStatus::PastDue,
        ], true);
    }

    /**
     * @return array{max_branches: int|null, max_staff: int|null, features: array<string, bool>, plan_slug: string|null, bullets: list<string>}
     */
    public function entitlementsForOrganization(Organization $organization): array
    {
        $cacheKey = 'feature_access.org.'.$organization->id;

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($organization) {
            $subscription = $organization->currentSubscription()->with('plan')->first();
            $plan = $subscription?->plan;
            $slug = $plan?->slug ?? 'basic';
            $defaults = $this->defaultsForSlug($slug);
            $parsed = $this->parsePlanFeatures($plan);

            return [
                'max_branches' => $parsed['max_branches'] ?? $defaults['max_branches'],
                'max_staff' => $parsed['max_staff'] ?? $defaults['max_staff'],
                'features' => array_merge($defaults['features'], $parsed['features']),
                'plan_slug' => $slug,
                'bullets' => $parsed['bullets'],
            ];
        });
    }

    public function entitlementsForUser(User $user): array
    {
        $user->loadMissing('organization');
        $organization = $user->organization;

        if ($organization === null) {
            return $this->defaultsForSlug('basic') + ['plan_slug' => null, 'bullets' => []];
        }

        return $this->entitlementsForOrganization($organization);
    }

    public function can(User $user, PlanFeature|string $feature): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $key = $feature instanceof PlanFeature ? $feature->value : $feature;
        $entitlements = $this->entitlementsForUser($user);

        return (bool) ($entitlements['features'][$key] ?? false);
    }

    public function ensureCan(User $user, PlanFeature|string $feature): void
    {
        if (! $this->can($user, $feature)) {
            $label = $feature instanceof PlanFeature ? $feature->label() : $feature;

            throw ValidationException::withMessages([
                'plan' => "Your current plan does not include {$label}. Upgrade to unlock this feature.",
            ]);
        }
    }

    public function maxBranches(User $user): ?int
    {
        return $this->entitlementsForUser($user)['max_branches'];
    }

    public function maxStaff(User $user): ?int
    {
        return $this->entitlementsForUser($user)['max_staff'];
    }

    public function forgetOrganization(int $organizationId): void
    {
        Cache::forget('feature_access.org.'.$organizationId);
    }

    public function forgetPlan(Plan $plan): void
    {
        $plan->subscriptions()
            ->pluck('organization_id')
            ->unique()
            ->each(fn ($id) => $this->forgetOrganization((int) $id));
    }

    /**
     * @return array{max_branches?: int|null, max_staff?: int|null, features: array<string, bool>, bullets: list<string>}
     */
    private function parsePlanFeatures(?Plan $plan): array
    {
        $raw = $plan?->features ?? [];

        if (! is_array($raw)) {
            return ['features' => [], 'bullets' => []];
        }

        // Structured: ['bullets' => [], 'entitlements' => [...]]
        if (isset($raw['entitlements']) && is_array($raw['entitlements'])) {
            $ent = $raw['entitlements'];
            $features = [];
            foreach (PlanFeature::moduleFeatures() as $feature) {
                if (array_key_exists($feature->value, $ent)) {
                    $features[$feature->value] = (bool) $ent[$feature->value];
                }
            }

            return [
                'max_branches' => array_key_exists('max_branches', $ent)
                    ? ($ent['max_branches'] === null ? null : (int) $ent['max_branches'])
                    : null,
                'max_staff' => array_key_exists('max_staff', $ent)
                    ? ($ent['max_staff'] === null ? null : (int) $ent['max_staff'])
                    : null,
                'features' => $features,
                'bullets' => array_values(array_filter(
                    array_map('strval', $raw['bullets'] ?? []),
                    fn ($b) => $b !== '',
                )),
            ];
        }

        // Legacy: list of marketing bullet strings only
        $bullets = [];
        foreach ($raw as $item) {
            if (is_string($item) && $item !== '') {
                $bullets[] = $item;
            }
        }

        return ['features' => [], 'bullets' => $bullets];
    }
}
