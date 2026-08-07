<x-layouts.business-admin title="Purchase Orders" active="purchase-orders">
    <x-admin.toolbar title="Purchase Orders" description="Create, approve and receive supplier orders — stock and finance sync automatically.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'create-po')">Create PO</x-admin.button>
    </x-admin.toolbar>

    <div class="admin-filter-pills mb-4">
        @foreach (['' => 'All', 'draft' => 'Draft', 'pending' => 'Pending', 'approved' => 'Approved', 'ordered' => 'Ordered', 'partial' => 'Partial', 'received' => 'Received'] as $value => $label)
            <a href="{{ route('business-admin.purchase-orders.index', $value ? ['status' => $value] : []) }}" @class(['admin-filter-pill', ($status ?? '') === $value ? 'admin-filter-pill-active' : 'admin-filter-pill-inactive'])>{{ $label }}</a>
        @endforeach
    </div>

    @if ($orders->isEmpty())
        <x-admin.empty-state title="No purchase orders" description="Create a PO when stock runs low." />
    @else
        <x-admin.table-shell sticky>
            <thead>
                <tr>
                    <th class="px-4 py-3">PO #</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Supplier</th>
                    <th class="hidden px-4 py-3 md:table-cell">Status</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="admin-table-stack-title px-4 py-3.5 font-medium" data-label="PO #">{{ $order->po_number }}</td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Supplier">{{ $order->supplier?->name }}</td>
                        <td class="hidden px-4 py-3.5 md:table-cell" data-label="Status">{{ $order->status->label() }}</td>
                        <td class="px-4 py-3.5" data-label="Total">£{{ number_format((float) $order->total, 2) }}</td>
                        <td class="admin-table-stack-actions px-4 py-3.5" data-label="">
                            <x-admin.table-action :href="route('business-admin.purchase-orders.show', $order)" variant="neutral">View</x-admin.table-action>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-admin.table-shell>
        <div class="admin-pagination mt-4">{{ $orders->links() }}</div>
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
