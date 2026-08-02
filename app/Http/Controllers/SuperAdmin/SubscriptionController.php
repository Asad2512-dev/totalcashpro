<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\SuperAdmin\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $service) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Create Subscription',
            'active' => 'subscriptions',
            'action' => route('super-admin.subscriptions.store'),
            'cancelRoute' => route('super-admin.subscriptions'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'organization_id' => ['required', 'exists:organizations,id'],
            'plan_id' => ['required', 'exists:plans,id'],
            'status' => ['required', 'in:trialing,active,past_due,suspended,cancelled,expired,free,lifetime'],
        ]);

        $data['starts_at'] = now();
        $data['current_period_start'] = now();
        $data['current_period_end'] = now()->addMonth();

        if ($data['status'] === 'trialing') {
            $data['trial_starts_at'] = now();
            $data['trial_ends_at'] = now()->addDays(14);
            $data['current_period_end'] = now()->addDays(14);
        }

        if ($data['status'] === 'lifetime') {
            $data['current_period_end'] = null;
        }

        if ($data['status'] === 'free') {
            $data['current_period_end'] = now()->addYear();
        }

        $this->service->create($data);

        return redirect()->route('super-admin.subscriptions')->with('status', 'Subscription created.');
    }

    public function show(Subscription $subscription): View
    {
        $subscription->load(['organization', 'plan']);

        return view('admin.crud.show', [
            'title' => 'Subscription #'.$subscription->id,
            'active' => 'subscriptions',
            'backRoute' => route('super-admin.subscriptions'),
            'fields' => [
                ['label' => 'Business', 'value' => $subscription->organization?->name ?? '—'],
                ['label' => 'Plan', 'value' => $subscription->plan?->name ?? '—'],
                ['label' => 'Status', 'value' => $subscription->status?->label() ?? '—'],
                ['label' => 'Period end', 'value' => $subscription->current_period_end?->format('d M Y') ?? '—'],
                ['label' => 'Trial end', 'value' => $subscription->trial_ends_at?->format('d M Y') ?? '—'],
            ],
            'actions' => view('admin.partials.subscription-actions', compact('subscription')),
        ]);
    }

    public function changePlan(Request $request, Subscription $subscription): RedirectResponse
    {
        $data = $request->validate(['plan_id' => ['required', 'exists:plans,id']]);
        $plan = Plan::query()->findOrFail($data['plan_id']);
        $this->service->changePlan($subscription, $plan);

        return back()->with('status', 'Plan changed.');
    }

    public function activate(Subscription $subscription): RedirectResponse
    {
        $this->service->setStatus($subscription, SubscriptionStatus::Active);

        return back()->with('status', 'Subscription activated.');
    }

    public function pause(Subscription $subscription): RedirectResponse
    {
        $this->service->setStatus($subscription, SubscriptionStatus::Suspended);

        return back()->with('status', 'Subscription suspended.');
    }

    public function cancel(Subscription $subscription): RedirectResponse
    {
        $this->service->setStatus($subscription, SubscriptionStatus::Cancelled);

        return back()->with('status', 'Subscription cancelled.');
    }

    public function resume(Subscription $subscription): RedirectResponse
    {
        $this->service->setStatus($subscription, SubscriptionStatus::Active);

        return back()->with('status', 'Subscription resumed.');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fields(): array
    {
        return [
            ['name' => 'organization_id', 'type' => 'select', 'label' => 'Organization', 'options' => Organization::query()->orderBy('name')->pluck('name', 'id')->all()],
            ['name' => 'plan_id', 'type' => 'select', 'label' => 'Plan', 'options' => Plan::query()->orderBy('sort_order')->pluck('name', 'id')->all()],
            ['name' => 'status', 'type' => 'select', 'options' => [
                'trialing' => 'Trial',
                'active' => 'Paid',
                'free' => 'Free',
                'lifetime' => 'Lifetime',
                'past_due' => 'Past due',
                'suspended' => 'Suspended',
                'cancelled' => 'Cancelled',
                'expired' => 'Expired',
            ]],
        ];
    }
}
