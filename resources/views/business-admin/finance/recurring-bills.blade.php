<x-layouts.business-admin title="Recurring bills" active="finance">
    <x-finance.page-header title="Recurring bills" description="Templates that auto-generate bills on schedule.">
        <x-slot:actions>
            <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-recurring')">Add</x-admin.button>
        </x-slot:actions>
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-recurring')">Add template</x-admin.button>
    </x-finance.page-header>
    <x-finance.nav active="recurring-bills" />

    @if ($templates->isEmpty())
        <x-admin.empty-state title="No recurring templates" description="Create rent, utilities and subscription templates here." />
    @else
        <x-admin.table-shell>
            <thead>
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Vendor</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="hidden px-4 py-3 md:table-cell">Frequency</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Next due</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($templates as $template)
                    <tr>
                        <td class="admin-table-stack-title px-4 py-3.5 font-medium" data-label="Title">{{ $template->title }}</td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Vendor">{{ $template->vendor ?: '—' }}</td>
                        <td class="px-4 py-3.5" data-label="Amount">£{{ number_format((float) $template->amount, 2) }}</td>
                        <td class="hidden px-4 py-3.5 capitalize md:table-cell" data-label="Frequency">{{ $template->frequency->value }}</td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Next due">{{ $template->next_due_date->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-admin.table-shell>
    @endif

    <x-admin.modal name="add-recurring" title="Add recurring bill">
        <form method="POST" action="{{ route('business-admin.finance.recurring-bills.store') }}" class="admin-form-grid">
            @csrf
            <x-admin.input name="title" label="Title" required />
            <x-admin.input name="vendor" label="Vendor" />
            <x-admin.input type="number" step="0.01" min="0.01" name="amount" label="Amount (£)" required />
            <div>
                <label class="text-sm font-semibold">Frequency</label>
                <select name="frequency" class="admin-input mt-2 w-full" required>
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <x-admin.input type="date" name="next_due_date" label="Next due date" required />
            <div class="flex justify-end gap-2">
                <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                <x-admin.button type="submit">Save</x-admin.button>
            </div>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
