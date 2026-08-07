<x-layouts.business-admin title="Purchase Invoices" active="finance">
    <x-admin.toolbar title="Purchase Invoices" description="Supplier purchase invoices with VAT breakdown.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-purchase-invoice')">Add invoice</x-admin.button>
    </x-admin.toolbar>
    <x-finance.nav active="purchase-invoices" />

    @if ($invoices->isEmpty())
        <x-admin.empty-state title="No purchase invoices" description="Record supplier invoices awaiting payment." />
    @else
        <div class="admin-card overflow-hidden">
            <div class="admin-table-wrap">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Invoice</th>
                            <th class="px-4 py-3">Supplier</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Net / VAT / Gross</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $invoice->invoice_no }}</td>
                                <td class="px-4 py-3">{{ $invoice->supplier?->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $invoice->invoice_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">£{{ number_format((float) $invoice->net_amount, 2) }} / £{{ number_format((float) $invoice->vat_amount, 2) }} / £{{ number_format((float) $invoice->gross_amount, 2) }}</td>
                                <td class="px-4 py-3 capitalize">{{ $invoice->status?->value ?? $invoice->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $invoices->links() }}</div>
    @endif

    <x-admin.modal name="add-purchase-invoice" title="Add purchase invoice">
        <form method="POST" action="{{ route('business-admin.finance.purchase-invoices.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <label class="block text-sm font-medium">Supplier
                <select name="supplier_id" class="admin-input mt-1 w-full" required>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </label>
            <x-admin.form-field label="Invoice number" name="invoice_no" required />
            <x-admin.form-field label="Gross amount (£)" name="gross_amount" type="number" step="0.01" required />
            <x-admin.form-field label="VAT rate (%)" name="vat_rate" type="number" step="0.01" value="20" />
            <x-admin.form-field label="Invoice date" name="invoice_date" type="date" :value="now()->toDateString()" required />
            <x-admin.form-field label="Due date" name="due_date" type="date" />
            <x-admin.form-field label="Invoice file" name="invoice" type="file" />
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="submit">Save</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
