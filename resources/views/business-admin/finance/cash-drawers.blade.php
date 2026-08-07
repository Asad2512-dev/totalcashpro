<x-layouts.business-admin title="Cash drawers" active="finance">
    <x-admin.toolbar title="Cash drawers" description="Manage till drawer opening balances per branch." />
    <x-finance.nav active="cash-drawers" />

    @if ($drawers->isEmpty())
        <x-admin.empty-state title="No cash drawers" description="Drawers are created automatically when branches are added." />
    @else
        <div class="space-y-4">
            @foreach ($drawers as $drawer)
                <x-admin.card>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $drawer->name }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ $drawer->branch?->name ?? 'Branch' }} · Current £{{ number_format((float) $drawer->current_balance, 2) }}</p>
                        </div>
                        <form method="POST" action="{{ route('business-admin.finance.cash-drawers.update', $drawer) }}" class="flex flex-wrap items-end gap-3">
                            @csrf
                            @method('PUT')
                            <x-admin.input type="number" step="0.01" min="0" name="opening_balance" label="Set balance (£)" :value="number_format((float) $drawer->current_balance, 2, '.', '')" />
                            <x-admin.button type="submit" size="sm">Update</x-admin.button>
                        </form>
                    </div>
                </x-admin.card>
            @endforeach
        </div>
    @endif
</x-layouts.business-admin>
