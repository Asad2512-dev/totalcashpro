<x-layouts.business-admin title="Recurring bills" active="finance">
    <x-admin.toolbar title="Recurring bills" description="Templates that auto-generate bills on schedule.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-recurring')">Add template</x-admin.button>
    </x-admin.toolbar>
    <x-finance.nav active="recurring-bills" />

    @if ($templates->isEmpty())
        <x-admin.empty-state title="No recurring templates" description="Create rent, utilities and subscription templates here." />
    @else
        <x-admin.card :padding="false">
            <div class="admin-table-wrap">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Title</th>
                            <th class="px-4 py-3">Vendor</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Frequency</th>
                            <th class="px-4 py-3">Next due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($templates as $template)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $template->title }}</td>
                                <td class="px-4 py-3">{{ $template->vendor ?: '—' }}</td>
                                <td class="px-4 py-3">£{{ number_format((float) $template->amount, 2) }}</td>
                                <td class="px-4 py-3 capitalize">{{ $template->frequency->value }}</td>
                                <td class="px-4 py-3">{{ $template->next_due_date->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin.card>
    @endif

    <x-admin.modal name="add-recurring" title="Add recurring bill">
        <form method="POST" action="{{ route('business-admin.finance.recurring-bills.store') }}" class="space-y-4">
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
