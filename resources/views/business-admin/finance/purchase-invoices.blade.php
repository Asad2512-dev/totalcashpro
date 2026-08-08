<x-layouts.business-admin title="Purchase Invoices" active="finance">
    <x-finance.page-header title="Purchase Invoices" description="Supplier purchase invoices with VAT breakdown.">
        <x-slot:actions>
            <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-purchase-invoice')">Add</x-admin.button>
        </x-slot:actions>
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-purchase-invoice')">Add invoice</x-admin.button>
    </x-finance.page-header>
    <x-finance.nav active="purchase-invoices" />

    @if ($invoices->isEmpty())
        <x-admin.empty-state title="No purchase invoices" description="Record supplier invoices awaiting payment." />
    @else
        <x-admin.table-shell sticky>
            <thead>
                <tr>
                    <th class="px-4 py-3">Invoice</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Supplier</th>
                    <th class="hidden px-4 py-3 md:table-cell">Date</th>
                    <th class="px-4 py-3">Net / VAT / Gross</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td class="admin-table-stack-title px-4 py-3.5 font-medium" data-label="Invoice">{{ $invoice->invoice_no }}</td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Supplier">{{ $invoice->supplier?->name ?? '—' }}</td>
                        <td class="hidden px-4 py-3.5 md:table-cell" data-label="Date">{{ $invoice->invoice_date->format('d M Y') }}</td>
                        <td class="px-4 py-3.5" data-label="Amounts">£{{ number_format((float) $invoice->net_amount, 2) }} / £{{ number_format((float) $invoice->vat_amount, 2) }} / £{{ number_format((float) $invoice->gross_amount, 2) }}</td>
                        <td class="hidden px-4 py-3.5 capitalize sm:table-cell" data-label="Status">{{ $invoice->status?->value ?? $invoice->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-admin.table-shell>
        <div class="admin-pagination mt-4">{{ $invoices->links() }}</div>
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
