<x-layouts.business-admin title="Bills" active="finance">
    <x-admin.toolbar title="Bills" description="Recurring and one-off bills with approval workflow.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-bill')">Add bill</x-admin.button>
    </x-admin.toolbar>
    <x-finance.nav active="bills" />

    @if ($bills->isEmpty())
        <x-admin.empty-state title="No bills" description="Track rent, utilities and other recurring costs." />
    @else
        <x-admin.table-shell sticky>
            <thead>
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Vendor</th>
                    <th class="hidden px-4 py-3 md:table-cell">Due</th>
                    <th class="px-4 py-3">Gross</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Status</th>
                    <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills as $bill)
                    <tr>
                        <td class="admin-table-stack-title px-4 py-3.5 font-medium" data-label="Title">{{ $bill->title }}</td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Vendor">{{ $bill->vendor ?: '—' }}</td>
                        <td class="hidden px-4 py-3.5 md:table-cell" data-label="Due">{{ $bill->due_date->format('d M Y') }}</td>
                        <td class="px-4 py-3.5" data-label="Gross">£{{ number_format((float) $bill->gross_amount, 2) }}</td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Status">{{ $bill->status->label() }}</td>
                        <td class="admin-table-stack-actions px-4 py-3.5" data-label="">
                            <x-admin.table-actions>
                                @if ($bill->status->value === 'draft')
                                    <form method="POST" action="{{ route('business-admin.finance.bills.approve', $bill) }}">
                                        @csrf
                                        <x-admin.table-action type="submit">Approve</x-admin.table-action>
                                    </form>
                                @elseif (in_array($bill->status->value, ['approved', 'pending', 'overdue'], true))
                                    <form method="POST" action="{{ route('business-admin.finance.bills.paid', $bill) }}">
                                        @csrf
                                        <x-admin.table-action type="submit" variant="success">Mark paid</x-admin.table-action>
                                    </form>
                                @endif
                            </x-admin.table-actions>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-admin.table-shell>
        <div class="admin-pagination mt-4">{{ $bills->links() }}</div>
    @endif

    <x-admin.modal name="add-bill" title="Add bill">
        <form method="POST" action="{{ route('business-admin.finance.bills.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <x-admin.form-field label="Title" name="title" required />
            <x-admin.form-field label="Vendor" name="vendor" />
            <label class="block text-sm font-medium">Category
                <select name="category" class="admin-input mt-1 w-full">
                    @foreach ($categories as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <x-admin.form-field label="Gross amount (£)" name="gross_amount" type="number" step="0.01" required />
            <x-admin.form-field label="VAT rate (%)" name="vat_rate" type="number" step="0.01" value="20" />
            <x-admin.form-field label="Due date" name="due_date" type="date" required />
            <x-admin.form-field label="Invoice upload" name="invoice" type="file" />
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="submit">Save draft</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
