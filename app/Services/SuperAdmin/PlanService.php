<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Concerns\LogsAdminActions;
use App\Contracts\ServiceInterface;
use App\Models\Plan;
use Illuminate\Support\Str;

final class PlanService implements ServiceInterface
{
    use LogsAdminActions;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Plan
    {
        $data['slug'] = Str::slug($data['slug'] ?? $data['name']);
        $data['features'] = $this->normalizeFeatures($data['features'] ?? []);

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
            $data['features'] = $this->normalizeFeatures($data['features']);
        }
        if (isset($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        $plan->update($data);
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
     * @param  array<int, string>|string  $features
     * @return list<string>
     */
    private function normalizeFeatures(array|string $features): array
    {
        if (is_string($features)) {
            $features = preg_split('/\r\n|\r|\n/', $features) ?: [];
        }

        return array_values(array_filter(array_map('trim', $features)));
    }
}
