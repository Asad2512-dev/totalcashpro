<x-layouts.business-admin :title="$customer->name" active="crm">
    <x-admin.toolbar description="Customer profile, timeline and visit history.">
        <x-admin.button size="sm" variant="secondary" :href="route('business-admin.crm')">Back to list</x-admin.button>
    </x-admin.toolbar>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <x-admin.card title="Details">
                <form method="POST" action="{{ route('business-admin.crm.update', $customer) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <x-admin.input name="name" label="Name" :value="old('name', $customer->name)" required />
                    <x-admin.input type="email" name="email" label="Email" :value="old('email', $customer->email)" />
                    <x-admin.input name="phone" label="Phone" :value="old('phone', $customer->phone)" />
                    <x-admin.textarea name="notes" label="Summary notes" rows="3">{{ old('notes', $customer->notes) }}</x-admin.textarea>
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Marketing preferences</p>
                        <x-admin.checkbox name="marketing_email" label="Email marketing" :checked="old('marketing_email', $customer->marketing_preferences['email'] ?? false)" />
                        <x-admin.checkbox name="marketing_sms" label="SMS marketing" :checked="old('marketing_sms', $customer->marketing_preferences['sms'] ?? false)" />
                    </div>
                    <div class="flex gap-2">
                        <x-admin.button type="submit" size="sm">Save changes</x-admin.button>
                    </div>
                </form>
                <form method="POST" action="{{ route('business-admin.crm.destroy', $customer) }}" class="mt-4" onsubmit="return confirm('Remove this customer?')">
                    @csrf
                    @method('DELETE')
                    <x-admin.button type="submit" size="sm" variant="secondary">Remove customer</x-admin.button>
                </form>
            </x-admin.card>
        </div>

        <div class="space-y-6 lg:col-span-2">
            <x-admin.card title="Add note">
                <form method="POST" action="{{ route('business-admin.crm.notes.store', $customer) }}" class="space-y-3">
                    @csrf
                    <x-admin.textarea name="body" label="Note" rows="3" required />
                    <x-admin.button type="submit" size="sm">Add note</x-admin.button>
                </form>
            </x-admin.card>

            <x-admin.card title="Record visit">
                <form method="POST" action="{{ route('business-admin.crm.visits.store', $customer) }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    <x-admin.input type="datetime-local" name="visited_at" label="Visit date" :value="old('visited_at', now()->format('Y-m-d\TH:i'))" />
                    <x-admin.input type="number" step="0.01" min="0" name="spend_amount" label="Spend (£)" />
                    <div class="sm:col-span-2">
                        <x-admin.textarea name="notes" label="Visit notes" rows="2" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-admin.button type="submit" size="sm">Record visit</x-admin.button>
                    </div>
                </form>
            </x-admin.card>

            <x-admin.card title="Timeline">
                @if ($timeline->isEmpty())
                    <x-admin.empty-state title="No activity yet" description="Notes and visits will appear here." />
                @else
                    <ol class="space-y-4">
                        @foreach ($timeline as $entry)
                            <li class="border-l-2 border-primary-200 pl-4 dark:border-primary-800">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $entry['at']->format('d M Y, H:i') }}</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $entry['title'] }}</p>
                                @if ($entry['body'])
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $entry['body'] }}</p>
                                @endif
                                @if ($entry['meta'])
                                    <p class="mt-1 text-xs text-gray-500">{{ $entry['meta'] }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            </x-admin.card>
        </div>
    </div>
</x-layouts.business-admin>
