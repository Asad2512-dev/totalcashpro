<x-layouts.business-admin title="Income" active="finance">
    <x-finance.page-header title="Income" description="Track sales and other inflows with VAT split.">
        <x-slot:actions>
            <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-income')">Add</x-admin.button>
        </x-slot:actions>
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-income')">Add income</x-admin.button>
    </x-finance.page-header>
    <x-finance.nav active="income" />

    @if ($entries->isEmpty())
        <x-admin.empty-state title="No income entries" description="Record manual income or approve cash-up linked entries." />
    @else
        <x-admin.table-shell>
            <thead>
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Date</th>
                    <th class="hidden px-4 py-3 md:table-cell">Net</th>
                    <th class="hidden px-4 py-3 lg:table-cell">VAT</th>
                    <th class="px-4 py-3">Gross</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Status</th>
                    <th class="px-4 py-3"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $entry)
                    <tr>
                        <td class="admin-table-stack-title px-4 py-3.5 font-medium" data-label="Title">{{ $entry->title }}</td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Date">{{ $entry->income_date->format('d M Y') }}</td>
                        <td class="hidden px-4 py-3.5 md:table-cell" data-label="Net">£{{ number_format((float) $entry->net_amount, 2) }}</td>
                        <td class="hidden px-4 py-3.5 lg:table-cell" data-label="VAT">£{{ number_format((float) $entry->vat_amount, 2) }}</td>
                        <td class="px-4 py-3.5" data-label="Gross">£{{ number_format((float) $entry->gross_amount, 2) }}</td>
                        <td class="hidden px-4 py-3.5 capitalize sm:table-cell" data-label="Status">{{ $entry->status->label() }}</td>
                        <td class="admin-table-stack-actions px-4 py-3.5" data-label="">
                            <x-admin.table-actions>
                                @if ($entry->status->value === 'draft')
                                    <form method="POST" action="{{ route('business-admin.finance.income.approve', $entry) }}">
                                        @csrf
                                        <x-admin.table-action type="submit">Approve</x-admin.table-action>
                                    </form>
                                @elseif ($entry->status->value === 'approved')
                                    <form method="POST" action="{{ route('business-admin.finance.income.paid', $entry) }}">
                                        @csrf
                                        <x-admin.table-action type="submit" variant="success">Mark received</x-admin.table-action>
                                    </form>
                                @endif
                            </x-admin.table-actions>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-admin.table-shell>
        <div class="admin-pagination mt-4">{{ $entries->links() }}</div>
    @endif

    <x-admin.modal name="add-income" title="Add income">
        <form method="POST" action="{{ route('business-admin.finance.income.store') }}" enctype="multipart/form-data" class="admin-form-grid">
            @csrf
            <div class="sm:col-span-2">
                <x-admin.form-field label="Title" name="title" required />
            </div>
            <x-admin.form-field label="Gross amount (£)" name="gross_amount" type="number" step="0.01" required />
            <x-admin.form-field label="VAT rate (%)" name="vat_rate" type="number" step="0.01" value="20" />
            <x-admin.form-field label="Income date" name="income_date" type="date" :value="now()->toDateString()" required />
            <div class="sm:col-span-2">
                <x-admin.form-field label="Notes" name="notes" type="textarea" />
            </div>
            <div class="sm:col-span-2">
                <x-admin.form-field label="Receipt" name="receipt" type="file" />
            </div>
            <div class="sm:col-span-2">
                <x-admin.form-actions class="justify-end pt-2">
                    <x-admin.button type="submit">Save draft</x-admin.button>
                </x-admin.form-actions>
            </div>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
