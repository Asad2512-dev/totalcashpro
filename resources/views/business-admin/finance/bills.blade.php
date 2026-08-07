<x-layouts.business-admin title="Bills" active="finance">
    <x-admin.toolbar title="Bills" description="Recurring and one-off bills with approval workflow.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-bill')">Add bill</x-admin.button>
    </x-admin.toolbar>
    <x-finance.nav active="bills" />

    @if ($bills->isEmpty())
        <x-admin.empty-state title="No bills" description="Track rent, utilities and other recurring costs." />
    @else
        <div class="admin-card overflow-hidden">
            <div class="admin-table-wrap">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Vendor</th>
                            <th class="px-4 py-3">Due</th>
                            <th class="px-4 py-3">Gross</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bills as $bill)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $bill->title }}</td>
                                <td class="px-4 py-3">{{ $bill->vendor ?: '—' }}</td>
                                <td class="px-4 py-3">{{ $bill->due_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">£{{ number_format((float) $bill->gross_amount, 2) }}</td>
                                <td class="px-4 py-3">{{ $bill->status->label() }}</td>
                                <td class="px-4 py-3">
                                    @if ($bill->status->value === 'draft')
                                        <form method="POST" action="{{ route('business-admin.finance.bills.approve', $bill) }}" class="inline">@csrf<button class="text-primary-700 font-semibold">Approve</button></form>
                                    @elseif (in_array($bill->status->value, ['approved', 'pending', 'overdue'], true))
                                        <form method="POST" action="{{ route('business-admin.finance.bills.paid', $bill) }}" class="inline">@csrf<button class="text-primary-700 font-semibold">Mark paid</button></form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $bills->links() }}</div>
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
