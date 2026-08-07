<x-layouts.business-admin title="Expenses" active="finance">
    <x-admin.toolbar title="Expenses" description="Day-to-day spending with draft → approved → paid workflow.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-expense')">Add expense</x-admin.button>
    </x-admin.toolbar>
    <x-finance.nav active="expenses" />

    @if ($expenses->isEmpty())
        <x-admin.empty-state title="No expenses" description="Record supplies, marketing and other costs." />
    @else
        <div class="admin-card overflow-hidden">
            <div class="admin-table-wrap">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Net / VAT / Gross</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $expense)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $expense->title }}</td>
                                <td class="px-4 py-3">{{ $categories[$expense->category] ?? $expense->category }}</td>
                                <td class="px-4 py-3">{{ $expense->spent_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">£{{ number_format((float) $expense->net_amount, 2) }} / £{{ number_format((float) $expense->vat_amount, 2) }} / £{{ number_format((float) $expense->gross_amount, 2) }}</td>
                                <td class="px-4 py-3 capitalize">{{ $expense->status?->label() ?? $expense->status }}</td>
                                <td class="px-4 py-3">
                                    @if (($expense->status?->value ?? '') === 'draft')
                                        <form method="POST" action="{{ route('business-admin.finance.expenses.approve', $expense) }}" class="inline">@csrf<button class="text-primary-700 font-semibold">Approve</button></form>
                                    @elseif (($expense->status?->value ?? '') === 'approved')
                                        <form method="POST" action="{{ route('business-admin.finance.expenses.paid', $expense) }}" class="inline">@csrf<button class="text-primary-700 font-semibold">Mark paid</button></form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $expenses->links() }}</div>
    @endif

    <x-admin.modal name="add-expense" title="Add expense">
        <form method="POST" action="{{ route('business-admin.finance.expenses.store') }}" enctype="multipart/form-data" class="space-y-4">
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
