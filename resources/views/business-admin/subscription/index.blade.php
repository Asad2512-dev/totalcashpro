<x-layouts.business-admin title="Subscription" active="subscription">
    <x-admin.toolbar title="Subscription" description="Your current TotalCashPro plan from live billing data." />

    @if (! $subscription)
        <x-admin.empty-state title="No subscription" description="Contact support or request a plan upgrade." />
    @else
        <x-admin.card>
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500">Current plan</p>
                    <h3 class="mt-1 font-display text-2xl font-bold">{{ $plan?->name ?? 'Plan' }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ $organization?->name }}</p>
                </div>
                <x-admin.badge tone="success">{{ $subscription->status instanceof \BackedEnum ? $subscription->status->value : $subscription->status }}</x-admin.badge>
            </div>
            <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-3">
                <div>
                    <dt class="text-gray-500">Period start</dt>
                    <dd class="mt-1 font-semibold">{{ $subscription->current_period_start?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Period end</dt>
                    <dd class="mt-1 font-semibold">{{ $subscription->current_period_end?->format('d M Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Trial ends</dt>
                    <dd class="mt-1 font-semibold">{{ $subscription->trial_ends_at?->format('d M Y') ?? '—' }}</dd>
                </div>
            </dl>
        </x-admin.card>
    @endif
</x-layouts.business-admin>
