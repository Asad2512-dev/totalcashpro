<x-layouts.business-admin title="Receive goods" active="receiving">
    <x-admin.toolbar :title="'Receive · '.$order->po_number" :description="$order->supplier?->name">
        <a href="{{ route('business-admin.receiving.index') }}" class="text-sm font-semibold text-primary-700">← Receiving</a>
    </x-admin.toolbar>

    <form method="POST" action="{{ route('business-admin.receiving.store', $order) }}" class="space-y-4">
        @csrf
        @foreach ($order->lines as $index => $line)
            @if ($line->quantityOutstanding() > 0)
                <x-admin.card>
                    <p class="font-semibold">{{ $line->description }}</p>
                    <p class="text-xs text-gray-500">Ordered: {{ number_format((float) $line->quantity, 2) }} · Outstanding: {{ number_format($line->quantityOutstanding(), 2) }}</p>
                    <input type="hidden" name="lines[{{ $index }}][purchase_order_line_id]" value="{{ $line->id }}">
                    <div class="mt-3 grid gap-3 sm:grid-cols-3">
                        <x-admin.input type="number" step="0.001" name="lines[{{ $index }}][quantity_received]" label="Received" :value="$line->quantityOutstanding()" />
                        <x-admin.input type="number" step="0.001" name="lines[{{ $index }}][quantity_damaged]" label="Damaged" value="0" />
                        <x-admin.input type="number" step="0.001" name="lines[{{ $index }}][quantity_missing]" label="Missing" value="0" />
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Accepted = Received − Damaged. Inventory updates by accepted only.</p>
                </x-admin.card>
            @endif
        @endforeach
        <x-admin.textarea name="notes" label="Notes" rows="2" />
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="allow_over_delivery" value="1"> Allow over-delivery</label>
        <x-admin.button type="submit">Confirm receipt</x-admin.button>
    </form>
</x-layouts.business-admin>
