<x-layouts.business-admin title="Income" active="finance">
    <x-admin.toolbar title="Income" description="Track sales and other inflows with VAT split.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-income')">Add income</x-admin.button>
    </x-admin.toolbar>
    <x-finance.nav active="income" />

    @if ($entries->isEmpty())
        <x-admin.empty-state title="No income entries" description="Record manual income or approve cash-up linked entries." />
    @else
        <div class="admin-card overflow-hidden">
            <div class="admin-table-wrap">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Net</th>
                            <th class="px-4 py-3">VAT</th>
                            <th class="px-4 py-3">Gross</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entries as $entry)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $entry->title }}</td>
                                <td class="px-4 py-3">{{ $entry->income_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">£{{ number_format((float) $entry->net_amount, 2) }}</td>
                                <td class="px-4 py-3">£{{ number_format((float) $entry->vat_amount, 2) }}</td>
                                <td class="px-4 py-3">£{{ number_format((float) $entry->gross_amount, 2) }}</td>
                                <td class="px-4 py-3 capitalize">{{ $entry->status->label() }}</td>
                                <td class="px-4 py-3 space-x-2">
                                    @if ($entry->status->value === 'draft')
                                        <form method="POST" action="{{ route('business-admin.finance.income.approve', $entry) }}" class="inline">@csrf<button class="text-primary-700 font-semibold">Approve</button></form>
                                    @elseif ($entry->status->value === 'approved')
                                        <form method="POST" action="{{ route('business-admin.finance.income.paid', $entry) }}" class="inline">@csrf<button class="text-primary-700 font-semibold">Mark received</button></form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $entries->links() }}</div>
    @endif

    <x-admin.modal name="add-income" title="Add income">
        <form method="POST" action="{{ route('business-admin.finance.income.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <x-admin.form-field label="Title" name="title" required />
            <x-admin.form-field label="Gross amount (£)" name="gross_amount" type="number" step="0.01" required />
            <x-admin.form-field label="VAT rate (%)" name="vat_rate" type="number" step="0.01" value="20" />
            <x-admin.form-field label="Income date" name="income_date" type="date" :value="now()->toDateString()" required />
            <x-admin.form-field label="Notes" name="notes" type="textarea" />
            <x-admin.form-field label="Receipt" name="receipt" type="file" />
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="submit">Save draft</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
