<x-layouts.business-admin title="Supplier Payments" active="finance">
    <x-finance.page-header title="Supplier Payments" description="Payments made against purchase invoices.">
        <x-slot:actions>
            <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-payment')">Add</x-admin.button>
        </x-slot:actions>
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-payment')">Record payment</x-admin.button>
    </x-finance.page-header>
    <x-finance.nav active="supplier-payments" />

    @if ($payments->isEmpty())
        <x-admin.empty-state title="No supplier payments" description="Record bank transfers and card payments to suppliers." />
    @else
        <x-admin.table-shell sticky>
            <thead>
                <tr>
                    <th class="px-4 py-3">Invoice</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Supplier</th>
                    <th class="hidden px-4 py-3 md:table-cell">Date</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="hidden px-4 py-3 lg:table-cell">Reference</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td class="admin-table-stack-title px-4 py-3.5" data-label="Invoice">{{ $payment->supplierInvoice?->invoice_no ?? '—' }}</td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Supplier">{{ $payment->supplierInvoice?->supplier?->name ?? '—' }}</td>
                        <td class="hidden px-4 py-3.5 md:table-cell" data-label="Date">{{ $payment->payment_date->format('d M Y') }}</td>
                        <td class="px-4 py-3.5" data-label="Amount">£{{ number_format((float) $payment->gross_amount, 2) }}</td>
                        <td class="hidden px-4 py-3.5 lg:table-cell" data-label="Reference">{{ $payment->reference ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-admin.table-shell>
        <div class="admin-pagination mt-4">{{ $payments->links() }}</div>
    @endif

    <x-admin.modal name="add-payment" title="Record supplier payment">
        <form method="POST" action="{{ route('business-admin.finance.supplier-payments.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <label class="block text-sm font-medium">Purchase invoice
                <select name="supplier_invoice_id" class="admin-input mt-1 w-full" required>
                    @forelse ($invoices as $invoice)
                        <option value="{{ $invoice->id }}">{{ $invoice->invoice_no }} — {{ $invoice->supplier?->name }} (£{{ number_format((float) $invoice->gross_amount, 2) }})</option>
                    @empty
                        <option value="">No open invoices</option>
                    @endforelse
                </select>
            </label>
            <x-admin.form-field label="Payment date" name="payment_date" type="date" :value="now()->toDateString()" required />
            <x-admin.form-field label="Reference" name="reference" />
            <x-admin.form-field label="Receipt" name="receipt" type="file" />
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="submit">Record payment</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
