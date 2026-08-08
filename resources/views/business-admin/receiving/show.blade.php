<x-layouts.business-admin title="GRN {{ $grn->grn_number }}" active="receiving">
    <x-admin.toolbar :title="$grn->grn_number ?? 'GRN'" :description="$grn->purchaseOrder?->po_number">
        <a href="{{ route('business-admin.receiving.print', $grn) }}" target="_blank" class="text-sm font-semibold text-primary-700">Print</a>
    </x-admin.toolbar>

    <x-admin.card class="mb-4">
        <p class="text-sm">Supplier: {{ $grn->purchaseOrder?->supplier?->name }}</p>
        <p class="text-sm">Received: {{ $grn->received_at?->format('d M Y') }} by {{ $grn->receiver?->name }}</p>
    </x-admin.card>

    <x-admin.card>
        <x-admin.table :columns="['Item','Ordered','Received','Damaged','Missing','Accepted']" :rows="$grn->lines->map(fn ($l) => [
            $l->purchaseOrderLine?->description ?? '—',
            number_format((float) ($l->purchaseOrderLine?->quantity ?? 0), 2),
            number_format((float) $l->quantity_received, 2),
            number_format((float) $l->quantity_damaged, 2),
            number_format((float) $l->quantity_missing, 2),
            number_format((float) $l->quantity_accepted, 2),
        ])->all()" />
    </x-admin.card>
</x-layouts.business-admin>
