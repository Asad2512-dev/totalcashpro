<x-layouts.business-admin title="Payroll" active="payroll">
    <x-admin.toolbar title="Payroll" description="Live wage records for the selected branch.">
        @foreach (['current' => 'All', 'unpaid' => 'Unpaid', 'paid' => 'Paid'] as $key => $label)
            <a
                href="{{ route('business-admin.payroll', ['period' => $key]) }}"
                @class([
                    'rounded-xl px-3 py-2 text-sm font-semibold',
                    'bg-primary-600 text-white' => $period === $key,
                    'border border-gray-200 text-gray-700 dark:border-gray-700' => $period !== $key,
                ])
            >{{ $label }}</a>
        @endforeach
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-wage')">Add wage</x-admin.button>
    </x-admin.toolbar>

    @if ($wages->isEmpty())
        <x-admin.empty-state title="No wage records" description="Create wage entries from hours worked." />
    @else
        <div class="admin-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50/90 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">Staff</th>
                            <th class="px-4 py-3">Hours</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Paid date</th>
                            <th class="px-4 py-3">Notes</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($wages as $wage)
                            @php
                                $status = $wage->status instanceof \BackedEnum ? $wage->status->value : (string) $wage->status;
                                $pending = strcasecmp($status, 'Paid') !== 0;
                            @endphp
                            <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $wage->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ number_format((float) $wage->hours_worked, 2) }}h</td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">£{{ number_format((float) $wage->amount, 2) }}</td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $status }}</td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $wage->paid_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $wage->notes ?: '—' }}</td>
                                <td class="px-4 py-3.5">
                                    @if ($pending)
                                        <form method="POST" action="{{ route('business-admin.payroll.paid', $wage) }}">
                                            @csrf
                                            <button type="submit" class="font-semibold text-primary-700">Mark paid</button>
                                        </form>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $wages->links() }}</div>
    @endif

    <x-admin.modal name="add-wage" title="Add wage">
        <form method="POST" action="{{ route('business-admin.payroll.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Staff</label>
                <select name="user_id" required class="admin-input w-full">
                    @foreach ($staff as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}{{ $member->hourly_rate ? ' (£'.number_format((float) $member->hourly_rate, 2).'/h)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.input type="number" name="hours_worked" label="Hours worked" step="0.01" min="0" required />
                <x-admin.input type="number" name="hourly_rate" label="Hourly rate (£)" step="0.01" min="0" />
            </div>
            <x-admin.textarea name="notes" label="Notes" rows="2" />
            <div class="flex justify-end gap-2">
                <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                <x-admin.button type="submit">Save</x-admin.button>
            </div>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
