<x-layouts.business-admin title="Suppliers" active="suppliers">
    <x-admin.toolbar title="Suppliers & invoices" description="Live supplier list and invoices for the selected branch.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-supplier')">Add supplier</x-admin.button>
        <x-admin.button size="sm" variant="secondary" x-data @click="$dispatch('open-modal', 'add-invoice')">Add invoice</x-admin.button>
    </x-admin.toolbar>

    <div>
        <div class="mb-6 flex gap-6 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('business-admin.suppliers', ['tab' => 'suppliers']) }}" @class(['border-b-2 py-3 text-sm font-semibold', 'border-primary-600 text-primary-700' => $tab === 'suppliers', 'border-transparent text-gray-500' => $tab !== 'suppliers'])>Suppliers</a>
            <a href="{{ route('business-admin.suppliers', ['tab' => 'invoices']) }}" @class(['border-b-2 py-3 text-sm font-semibold', 'border-primary-600 text-primary-700' => $tab === 'invoices', 'border-transparent text-gray-500' => $tab !== 'invoices'])>Invoices</a>
        </div>

        @if ($tab === 'suppliers')
            @if ($suppliers->isEmpty())
                <x-admin.empty-state title="No suppliers yet" description="Add a supplier to start tracking invoices." />
            @else
                <x-admin.table
                    :columns="['Name', 'Contact', 'Email', 'Phone', 'Invoices']"
                    :rows="$suppliers->map(fn ($s) => [
                        $s->name,
                        $s->contact_name ?: '—',
                        $s->email ?: '—',
                        $s->phone ?: '—',
                        (string) $s->invoices_count,
                    ])->all()"
                />
            @endif
        @else
            @if ($invoices->isEmpty())
                <x-admin.empty-state title="No invoices yet" description="Add supplier invoices to track payables in real time." />
            @else
                <x-admin.table-shell sticky>
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Supplier</th>
                            <th class="hidden px-4 py-3 sm:table-cell">Invoice #</th>
                            <th class="hidden px-4 py-3 md:table-cell">Invoice date</th>
                            <th class="hidden px-4 py-3 lg:table-cell">Due</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="hidden px-4 py-3 sm:table-cell">Status</th>
                            <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            @php
                                $status = $invoice->status instanceof \BackedEnum ? $invoice->status->value : (string) $invoice->status;
                                $paid = strcasecmp($status, 'Paid') === 0;
                            @endphp
                            <tr>
                                <td class="admin-table-stack-title px-4 py-3.5" data-label="Supplier">{{ $invoice->supplier?->name ?? '—' }}</td>
                                <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Invoice #">{{ $invoice->invoice_no }}</td>
                                <td class="hidden px-4 py-3.5 md:table-cell" data-label="Invoice date">{{ $invoice->invoice_date?->format('d M Y') }}</td>
                                <td class="hidden px-4 py-3.5 lg:table-cell" data-label="Due">{{ $invoice->due_date?->format('d M Y') }}</td>
                                <td class="px-4 py-3.5" data-label="Amount">£{{ number_format((float) $invoice->amount, 2) }}</td>
                                <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Status">{{ $status }}</td>
                                <td class="admin-table-stack-actions px-4 py-3.5" data-label="">
                                    @unless ($paid)
                                        <form method="POST" action="{{ route('business-admin.suppliers.invoices.paid', $invoice) }}">
                                            @csrf
                                            <x-admin.table-action type="submit" variant="success">Mark paid</x-admin.table-action>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-admin.table-shell>
                <div class="admin-pagination mt-4">{{ $invoices->links() }}</div>
            @endif
        @endif
    </div>

    <x-admin.modal name="add-supplier" title="Add supplier">
        <form method="POST" action="{{ route('business-admin.suppliers.store') }}" class="space-y-4">
            @csrf
            <x-admin.input name="name" label="Supplier name" required />
            <x-admin.input name="contact_name" label="Contact name" />
            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.input type="email" name="email" label="Email" />
                <x-admin.input name="phone" label="Phone" />
            </div>
            <x-admin.textarea name="address" label="Address" rows="2" />
            <x-admin.textarea name="notes" label="Notes" rows="2" />
            <div class="flex justify-end gap-2">
                <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                <x-admin.button type="submit">Save</x-admin.button>
            </div>
        </form>
    </x-admin.modal>

    <x-admin.modal name="add-invoice" title="Add invoice">
        <form method="POST" action="{{ route('business-admin.suppliers.invoices.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                <select name="supplier_id" required class="admin-input w-full">
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <x-admin.input name="invoice_no" label="Invoice number" required />
            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.input type="date" name="invoice_date" label="Invoice date" required />
                <x-admin.input type="date" name="due_date" label="Due date" required />
            </div>
            <x-admin.input type="number" name="amount" label="Amount (£)" step="0.01" min="0" required />
            <x-admin.textarea name="description" label="Description" rows="2" />
            <div class="flex justify-end gap-2">
                <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                <x-admin.button type="submit">Save</x-admin.button>
            </div>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
