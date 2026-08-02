<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\SuperAdmin\PlanService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class PlanController extends Controller
{
    public function __construct(private readonly PlanService $service) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Create Plan',
            'active' => 'plans',
            'action' => route('super-admin.plans.store'),
            'cancelRoute' => route('super-admin.plans'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $this->service->create($data);

        return redirect()->route('super-admin.plans')->with('status', 'Plan created.');
    }

    public function edit(Plan $plan): View
    {
        return view('admin.crud.form', [
            'title' => 'Edit Plan',
            'active' => 'plans',
            'action' => route('super-admin.plans.update', $plan),
            'method' => 'PUT',
            'cancelRoute' => route('super-admin.plans'),
            'model' => $plan,
            'fields' => $this->fields($plan),
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $this->service->update($plan, $this->validated($request));

        return redirect()->route('super-admin.plans')->with('status', 'Plan updated.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $this->service->delete($plan);

        return redirect()->route('super-admin.plans')->with('status', 'Plan deleted.');
    }

    public function enable(Plan $plan): RedirectResponse
    {
        $this->service->setActive($plan, true);

        return back()->with('status', 'Plan enabled.');
    }

    public function disable(Plan $plan): RedirectResponse
    {
        $this->service->setActive($plan, false);

        return back()->with('status', 'Plan disabled.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'badge' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'billing_interval' => ['required', 'string', 'max:50'],
            'features' => ['nullable', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'is_public' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_public'] = $request->boolean('is_public', true);

        return $data;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fields(?Plan $plan = null): array
    {
        return [
            ['name' => 'name', 'value' => $plan?->name],
            ['name' => 'slug', 'value' => $plan?->slug],
            ['name' => 'badge', 'value' => $plan?->badge],
            ['name' => 'price_monthly', 'type' => 'number', 'label' => 'Monthly price', 'value' => $plan?->price_monthly ?? 19.99],
            ['name' => 'currency', 'value' => $plan?->currency ?? 'GBP'],
            ['name' => 'billing_interval', 'value' => $plan?->billing_interval ?? 'monthly'],
            ['name' => 'sort_order', 'type' => 'number', 'value' => $plan?->sort_order ?? 0],
            ['name' => 'description', 'type' => 'textarea', 'value' => $plan?->description, 'full' => true],
            ['name' => 'features', 'type' => 'textarea', 'label' => 'Features (one per line)', 'value' => implode("\n", $plan?->features ?? []), 'full' => true],
            ['name' => 'is_featured', 'type' => 'checkbox', 'label' => 'Featured plan', 'value' => $plan?->is_featured],
            ['name' => 'is_active', 'type' => 'checkbox', 'label' => 'Active', 'value' => $plan?->is_active ?? true],
            ['name' => 'is_public', 'type' => 'checkbox', 'label' => 'Public', 'value' => $plan?->is_public ?? true],
        ];
    }
}
