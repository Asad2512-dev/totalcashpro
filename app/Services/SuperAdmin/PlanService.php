<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Concerns\LogsAdminActions;
use App\Contracts\ServiceInterface;
use App\Models\Plan;
use App\Services\Billing\FeatureAccessService;
use Illuminate\Support\Str;

final class PlanService implements ServiceInterface
{
    use LogsAdminActions;

    public function __construct(private readonly FeatureAccessService $featureAccess) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Plan
    {
        $data['slug'] = Str::slug($data['slug'] ?? $data['name']);
        $data['features'] = $this->normalizeFeatures($data['features'] ?? [], $data['slug']);

        $plan = Plan::query()->create($data);
        $this->logAdminAction('plan.created', 'Plan created: '.$plan->name, $plan, null, $plan->toArray());

        return $plan;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Plan $plan, array $data): Plan
    {
        $old = $plan->toArray();
        if (isset($data['features'])) {
            $data['features'] = $this->normalizeFeatures(
                $data['features'],
                $data['slug'] ?? $plan->slug,
                is_array($plan->features) ? $plan->features : [],
            );
        }
        if (isset($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        $plan->update($data);
        $this->featureAccess->forgetPlan($plan);
        $this->logAdminAction('plan.updated', 'Plan updated: '.$plan->name, $plan, $old, $plan->fresh()?->toArray());

        return $plan->refresh();
    }

    public function setActive(Plan $plan, bool $active): Plan
    {
        $plan->update(['is_active' => $active]);
        $this->logAdminAction(
            $active ? 'plan.enabled' : 'plan.disabled',
            ($active ? 'Enabled' : 'Disabled').' plan '.$plan->name,
            $plan,
        );

        return $plan->refresh();
    }

    public function delete(Plan $plan): void
    {
        if ($plan->subscriptions()->exists()) {
            abort(422, 'Cannot delete a plan with existing subscriptions. Disable it instead.');
        }

        $snapshot = $plan->toArray();
        $name = $plan->name;
        $plan->delete();
        $this->logAdminAction('plan.deleted', 'Plan deleted: '.$name, null, $snapshot);
    }

    /**
     * Accepts marketing bullet text/lines or structured features and always stores
     * { bullets: string[], entitlements: array }.
     *
     * @param  array<mixed>|string  $features
     * @param  array<string, mixed>  $existing
     * @return array{bullets: list<string>, entitlements: array<string, mixed>}
     */
    private function normalizeFeatures(array|string $features, string $slug, array $existing = []): array
    {
        $defaults = $this->featureAccess->defaultsForSlug($slug);
        $existingEntitlements = [];
        if (isset($existing['entitlements']) && is_array($existing['entitlements'])) {
            $existingEntitlements = $existing['entitlements'];
        }

        if (is_array($features) && isset($features['entitlements'])) {
            $bullets = array_values(array_filter(array_map('strval', $features['bullets'] ?? [])));
            $entitlements = array_merge(
                [
                    'max_branches' => $defaults['max_branches'],
                    'max_staff' => $defaults['max_staff'],
                ],
                $defaults['features'],
                $existingEntitlements,
                $features['entitlements'],
            );

            return ['bullets' => $bullets, 'entitlements' => $entitlements];
        }

        if (is_string($features)) {
            $features = preg_split('/\r\n|\r|\n/', $features) ?: [];
        }

        $bullets = array_values(array_filter(array_map(
            static fn ($item) => is_string($item) ? trim($item) : '',
            $features,
        )));

        return [
            'bullets' => $bullets,
            'entitlements' => array_merge(
                [
                    'max_branches' => $defaults['max_branches'],
                    'max_staff' => $defaults['max_staff'],
                ],
                $defaults['features'],
                $existingEntitlements,
            ),
        ];
    }
}
