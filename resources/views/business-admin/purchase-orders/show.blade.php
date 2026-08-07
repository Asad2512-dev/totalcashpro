<x-layouts.business-admin title="PO {{ $order->po_number }}" active="purchase-orders">
    <x-admin.toolbar :title="'PO '.$order->po_number" :description="$order->supplier?->name ?? 'Purchase order'">
        <a href="{{ route('business-admin.purchase-orders') }}" class="text-sm font-semibold text-primary-700">← Back</a>
    </x-admin.toolbar>

    <div class="admin-stat-grid mb-6">
        <x-admin.card>
            <p class="text-xs font-semibold uppercase text-gray-500">Status</p>
            <p class="mt-2 text-xl font-bold text-primary-700">{{ $order->status->label() }}</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-semibold uppercase text-gray-500">Total</p>
            <p class="mt-2 text-xl font-bold">£{{ number_format((float) $order->total, 2) }}</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-semibold uppercase text-gray-500">Expected</p>
            <p class="mt-2 text-xl font-bold">{{ $order->expected_at?->format('d M Y') ?? '—' }}</p>
        </x-admin.card>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        @if ($order->status === \App\Enums\PurchaseOrderStatus::Draft)
            <form method="POST" action="{{ route('business-admin.purchase-orders.submit', $order) }}">@csrf<x-admin.button size="sm" type="submit">Submit</x-admin.button></form>
        @endif
        @if ($order->status === \App\Enums\PurchaseOrderStatus::Pending)
            <form method="POST" action="{{ route('business-admin.purchase-orders.approve', $order) }}">@csrf<x-admin.button size="sm" type="submit">Approve</x-admin.button></form>
        @endif
        @if ($order->status === \App\Enums\PurchaseOrderStatus::Approved)
            <form method="POST" action="{{ route('business-admin.purchase-orders.order', $order) }}">@csrf<x-admin.button size="sm" type="submit">Mark ordered</x-admin.button></form>
        @endif
        @if (in_array($order->status, [\App\Enums\PurchaseOrderStatus::Ordered, \App\Enums\PurchaseOrderStatus::PartiallyReceived], true))
            <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'receive-goods')">Receive goods</x-admin.button>
        @endif
        @if (! in_array($order->status, [\App\Enums\PurchaseOrderStatus::Received, \App\Enums\PurchaseOrderStatus::Cancelled], true))
            <form method="POST" action="{{ route('business-admin.purchase-orders.cancel', $order) }}" onsubmit="return confirm('Cancel this PO?')">@csrf<x-admin.button size="sm" type="submit" variant="secondary">Cancel</x-admin.button></form>
        @endif
    </div>

    <x-admin.card class="mb-6">
        <h3 class="font-display text-lg font-bold">Line items</h3>
        <x-admin.table
            class="mt-4"
            :columns="['Description', 'Qty', 'Received', 'Unit cost', 'Line total']"
            :rows="$order->lines->map(fn ($l) => [
                $l->description,
                number_format((float) $l->quantity, 2),
                number_format((float) $l->quantity_received, 2),
                '£'.number_format((float) $l->unit_cost, 2),
                '£'.number_format((float) $l->line_total, 2),
            ])->all()"
        />
    </x-admin.card>

    @if ($order->goodsReceivedNotes->isNotEmpty())
        <x-admin.card class="mb-6">
            <h3 class="font-display text-lg font-bold">Goods received</h3>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($order->goodsReceivedNotes as $grn)
                    <li class="rounded-xl border border-gray-100 px-3 py-2 dark:border-gray-700">
                        {{ $grn->received_at->format('d M Y') }} · received by {{ $grn->receiver?->name ?? 'System' }}
                    </li>
                @endforeach
            </ul>
        </x-admin.card>
    @endif

    @if ($order->supplierInvoice)
        <x-admin.card>
            <h3 class="font-display text-lg font-bold">Linked finance</h3>
            <p class="mt-2 text-sm">Invoice {{ $order->supplierInvoice->invoice_no }} · £{{ number_format((float) $order->supplierInvoice->gross_amount, 2) }} · {{ $order->supplierInvoice->status->label() }}</p>
        </x-admin.card>
    @endif

    <x-admin.modal name="receive-goods" title="Receive goods">
        <form method="POST" action="{{ route('business-admin.purchase-orders.receive', $order) }}" class="space-y-4">
            @csrf
            @foreach ($order->lines as $index => $line)
                @if ($line->quantityOutstanding() > 0)
                    <input type="hidden" name="lines[{{ $index }}][purchase_order_line_id]" value="{{ $line->id }}">
                    <div class="rounded-xl border border-gray-200 p-3 dark:border-gray-700">
                        <p class="font-semibold">{{ $line->description }}</p>
                        <p class="text-xs text-gray-500">Outstanding: {{ number_format($line->quantityOutstanding(), 2) }}</p>
                        <div class="mt-3 grid gap-3 sm:grid-cols-3">
                            <x-admin.input type="number" step="0.001" name="lines[{{ $index }}][quantity_received]" label="Received" :value="$line->quantityOutstanding()" />
                            <x-admin.input type="number" step="0.001" name="lines[{{ $index }}][quantity_damaged]" label="Damaged" value="0" />
                            <x-admin.input type="number" step="0.001" name="lines[{{ $index }}][quantity_missing]" label="Missing" value="0" />
                        </div>
                    </div>
                @endif
            @endforeach
            <x-admin.textarea name="notes" label="Notes" rows="2" />
            <div class="flex justify-end"><x-admin.button type="submit">Confirm receipt</x-admin.button></div>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
