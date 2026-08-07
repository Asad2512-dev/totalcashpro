<x-layouts.business-admin title="Purchase Orders" active="purchase-orders">
    <x-admin.toolbar title="Purchase Orders" description="Create, approve and receive supplier orders — stock and finance sync automatically.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'create-po')">Create PO</x-admin.button>
    </x-admin.toolbar>

    <div class="mb-4 flex flex-wrap gap-2">
        @foreach (['' => 'All', 'draft' => 'Draft', 'pending' => 'Pending', 'approved' => 'Approved', 'ordered' => 'Ordered', 'partial' => 'Partial', 'received' => 'Received'] as $value => $label)
            <a href="{{ route('business-admin.purchase-orders.index', $value ? ['status' => $value] : []) }}" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ ($status ?? '') === $value ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if ($orders->isEmpty())
        <x-admin.empty-state title="No purchase orders" description="Create a PO when stock runs low." />
    @else
        <x-admin.card :padding="false">
            <div class="admin-table-wrap">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">PO #</th>
                            <th class="px-4 py-3">Supplier</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $order->po_number }}</td>
                                <td class="px-4 py-3">{{ $order->supplier?->name }}</td>
                                <td class="px-4 py-3">{{ $order->status->label() }}</td>
                                <td class="px-4 py-3">£{{ number_format((float) $order->total, 2) }}</td>
                                <td class="px-4 py-3"><a href="{{ route('business-admin.purchase-orders.show', $order) }}" class="font-semibold text-primary-700">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin.card>
        <div class="mt-4">{{ $orders->links() }}</div>
    @endif

    <x-admin.modal name="create-po" title="Create purchase order">
        <form method="POST" action="{{ route('business-admin.purchase-orders.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium">Supplier</label>
                <select name="supplier_id" class="admin-input w-full" required>
                    @foreach ($meta['suppliers'] as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <x-admin.input type="date" name="expected_at" label="Expected delivery" />
            <x-admin.input name="lines[0][description]" label="Line description" required />
            <input type="hidden" name="lines[0][quantity]" value="1">
            <x-admin.input type="number" step="0.01" name="lines[0][unit_cost]" label="Unit cost (£)" required />
            <x-admin.textarea name="notes" label="Notes" rows="2" />
            <div class="flex justify-end"><x-admin.button type="submit">Save draft</x-admin.button></div>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
