<x-layouts.business-admin title="Choose your plan" active="subscription">
    <x-admin.toolbar title="Your trial has ended" description="Choose a monthly plan to continue using TotalCashPro. Payment integration coming soon — your selection will be saved." />

    @if ($trial)
        <x-admin.alert tone="warning" class="mb-6">
            Trial ended{{ $trial['trial_ends_at'] ? ' on '.$trial['trial_ends_at']->format('d M Y') : '' }}. Select Basic (£19.99/month) or Professional (£29.99/month) to continue.
        </x-admin.alert>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        @foreach ($plans as $plan)
            <x-admin.card>
                <h3 class="font-display text-xl font-bold text-gray-900">{{ $plan->name }}</h3>
                <p class="mt-2 text-3xl font-extrabold text-primary-600">£{{ number_format((float) $plan->price_monthly, 2) }}<span class="text-base font-medium text-gray-500">/month</span></p>
                <p class="mt-3 text-sm text-gray-600">{{ $plan->description }}</p>
                <form method="POST" action="{{ route('business-admin.subscription.choose-plan.store') }}" class="mt-6">
                    @csrf
                    <input type="hidden" name="plan" value="{{ $plan->slug }}">
                    <x-admin.button type="submit" class="w-full">Choose {{ $plan->name }}</x-admin.button>
                </form>
            </x-admin.card>
        @endforeach
    </div>

    <p class="mt-8 text-center text-sm text-gray-500">✔ No setup fees · ✔ Cancel anytime · Stripe billing coming soon</p>
</x-layouts.business-admin>
