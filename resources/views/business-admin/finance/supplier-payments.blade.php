<x-layouts.business-admin title="Supplier Payments" active="finance">
    <x-admin.toolbar title="Supplier Payments" description="Payments made against purchase invoices.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-payment')">Record payment</x-admin.button>
    </x-admin.toolbar>
    <x-finance.nav active="supplier-payments" />

    @if ($payments->isEmpty())
        <x-admin.empty-state title="No supplier payments" description="Record bank transfers and card payments to suppliers." />
    @else
        <div class="admin-card overflow-hidden">
            <div class="admin-table-wrap">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Invoice</th>
                            <th class="px-4 py-3">Supplier</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td class="px-4 py-3">{{ $payment->supplierInvoice?->invoice_no ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $payment->supplierInvoice?->supplier?->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">£{{ number_format((float) $payment->gross_amount, 2) }}</td>
                                <td class="px-4 py-3">{{ $payment->reference ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $payments->links() }}</div>
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
