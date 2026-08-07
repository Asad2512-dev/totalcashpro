<x-layouts.business-admin title="Expenses" active="finance">
    <x-admin.toolbar title="Expenses" description="Day-to-day spending with draft → approved → paid workflow.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-expense')">Add expense</x-admin.button>
    </x-admin.toolbar>
    <x-finance.nav active="expenses" />

    @if ($expenses->isEmpty())
        <x-admin.empty-state title="No expenses" description="Record supplies, marketing and other costs." />
    @else
        <x-admin.table-shell sticky>
            <thead>
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Category</th>
                    <th class="hidden px-4 py-3 md:table-cell">Date</th>
                    <th class="px-4 py-3">Amounts</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Status</th>
                    <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenses as $expense)
                    <tr>
                        <td class="admin-table-stack-title px-4 py-3.5 font-medium" data-label="Title">{{ $expense->title }}</td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Category">{{ $categories[$expense->category] ?? $expense->category }}</td>
                        <td class="hidden px-4 py-3.5 md:table-cell" data-label="Date">{{ $expense->spent_date->format('d M Y') }}</td>
                        <td class="px-4 py-3.5" data-label="Net / VAT / Gross">£{{ number_format((float) $expense->net_amount, 2) }} / £{{ number_format((float) $expense->vat_amount, 2) }} / £{{ number_format((float) $expense->gross_amount, 2) }}</td>
                        <td class="hidden px-4 py-3.5 capitalize sm:table-cell" data-label="Status">{{ $expense->status?->label() ?? $expense->status }}</td>
                        <td class="admin-table-stack-actions px-4 py-3.5" data-label="">
                            <x-admin.table-actions>
                                @if (($expense->status?->value ?? '') === 'draft')
                                    <form method="POST" action="{{ route('business-admin.finance.expenses.approve', $expense) }}">
                                        @csrf
                                        <x-admin.table-action type="submit">Approve</x-admin.table-action>
                                    </form>
                                @elseif (($expense->status?->value ?? '') === 'approved')
                                    <form method="POST" action="{{ route('business-admin.finance.expenses.paid', $expense) }}">
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
        <div class="admin-pagination mt-4">{{ $expenses->links() }}</div>
    @endif

    <x-admin.modal name="add-expense" title="Add expense">
        <form method="POST" action="{{ route('business-admin.finance.expenses.store') }}" enctype="multipart/form-data" class="admin-form-grid">
            @csrf
            <x-admin.form-field label="Title" name="title" required />
            <label class="block text-sm font-medium">Category
                <select name="category" class="admin-input mt-1 w-full">
                    @foreach ($categories as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <x-admin.form-field label="Gross amount (£)" name="gross_amount" type="number" step="0.01" required />
            <x-admin.form-field label="VAT rate (%)" name="vat_rate" type="number" step="0.01" value="20" />
            <x-admin.form-field label="Spent date" name="spent_date" type="date" :value="now()->toDateString()" required />
            <label class="block text-sm font-medium">Payment method
                <select name="payment_method" class="admin-input mt-1 w-full">
                    <option value="">—</option>
                    @foreach ($paymentMethods as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <x-admin.form-field label="Receipt" name="receipt" type="file" />
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="submit">Save draft</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
