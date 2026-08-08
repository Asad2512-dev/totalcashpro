<x-layouts.business-admin title="Weekly Stocktake" active="stocktake">
    <x-admin.toolbar title="Weekly stocktake" description="Review staff counts, adjust orders, and approve vendor splits.">
        <a href="{{ route('business-admin.inventory') }}" class="text-sm font-semibold text-primary-700 hover:underline">Inventory →</a>
    </x-admin.toolbar>

    <x-admin.table
        :columns="['Week', 'Branch', 'Staff', 'Items', 'Status', '']"
        :rows="$stocktakes->map(fn ($s) => [
            $s->week_start?->format('d M').' – '.$s->week_end?->format('d M Y'),
            $s->branch?->name ?? '—',
            $s->creator?->name ?? '—',
            $s->items->count(),
            $s->status?->label() ?? $s->status,
            'Review',
        ])->all()"
    />

    <div class="mt-4">{{ $stocktakes->links() }}</div>
</x-layouts.business-admin>
