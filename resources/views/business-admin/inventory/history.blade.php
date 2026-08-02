<x-layouts.business-admin title="Inventory History" active="inventory-history">
    <x-admin.toolbar title="Inventory history" description="Live stock count adjustments for the selected branch filter.">
        <form method="GET" class="inline-flex flex-wrap items-center gap-2">
            <x-admin.input type="date" name="from" :value="$from" />
            <x-admin.input type="date" name="to" :value="$to" />
            <x-admin.button type="submit" size="sm" variant="secondary">Filter</x-admin.button>
        </form>
        <x-admin.button size="sm" variant="secondary" :href="route('business-admin.inventory')">Back to inventory</x-admin.button>
    </x-admin.toolbar>

    @if ($counts->isEmpty())
        <x-admin.empty-state title="No count history" description="Stock counts appear here after you save adjustments on Inventory." />
    @else
        <x-admin.table
            :columns="['When', 'Item', 'Branch', 'Diff', 'New stock', 'By', 'Notes']"
            :rows="$counts->map(fn ($c) => [
                $c->created_at?->format('d M Y H:i'),
                $c->item?->name ?? '—',
                $c->branch?->name ?? '—',
                ($c->diff_pcs > 0 ? '+' : '').$c->diff_pcs,
                $c->new_pcs.' pcs',
                $c->creator?->name ?? '—',
                $c->notes ?: '—',
            ])->all()"
        />
        <div class="mt-4">{{ $counts->links() }}</div>
    @endif
</x-layouts.business-admin>
