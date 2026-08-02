<form method="POST" action="{{ route('super-admin.subscriptions.activate', $subscription) }}" class="inline">@csrf<x-admin.button size="sm" type="submit">Activate</x-admin.button></form>
<form method="POST" action="{{ route('super-admin.subscriptions.pause', $subscription) }}" class="inline">@csrf<x-admin.button size="sm" variant="secondary" type="submit">Pause</x-admin.button></form>
<form method="POST" action="{{ route('super-admin.subscriptions.resume', $subscription) }}" class="inline">@csrf<x-admin.button size="sm" variant="soft" type="submit">Resume</x-admin.button></form>
<form method="POST" action="{{ route('super-admin.subscriptions.cancel', $subscription) }}" class="inline">@csrf<x-admin.button size="sm" variant="danger" type="submit">Cancel</x-admin.button></form>
<form method="POST" action="{{ route('super-admin.subscriptions.change-plan', $subscription) }}" class="mt-3 flex flex-wrap items-end gap-2">
    @csrf
    <x-admin.select name="plan_id" label="Change plan">
        @foreach (\App\Models\Plan::query()->orderBy('sort_order')->get() as $plan)
            <option value="{{ $plan->id }}" @selected($subscription->plan_id === $plan->id)>{{ $plan->name }}</option>
        @endforeach
    </x-admin.select>
    <x-admin.button size="sm" variant="secondary" type="submit">Apply plan</x-admin.button>
</form>
