<x-layouts.business-admin title="Payroll" active="payroll">
    <x-admin.toolbar title="Payroll" description="Live wage records for the selected branch.">
        @foreach (['current' => 'All', 'unpaid' => 'Unpaid', 'paid' => 'Paid'] as $key => $label)
            <x-admin.nav-pill :href="route('business-admin.payroll', ['period' => $key])" :active="$period === $key">{{ $label }}</x-admin.nav-pill>
        @endforeach
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-wage')">Add wage</x-admin.button>
    </x-admin.toolbar>

    <div class="admin-stat-grid mb-6">
        <x-admin.stat label="Unpaid wages" :value="'£'.number_format((float) ($summary['unpaid_total'] ?? 0), 2)" :change="($summary['unpaid_count'] ?? 0).' pending'" tone="warning" />
        <x-admin.stat label="Paid this month" :value="'£'.number_format((float) ($summary['paid_month_total'] ?? 0), 2)" change="Current month" tone="success" />
        <x-admin.stat label="Records" :value="(string) $wages->total()" change="This filter" tone="neutral" />
    </div>

    @if ($wages->isEmpty())
        <x-admin.empty-state title="No wage records" description="Create wage entries from hours worked.">
            <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-wage')">Add wage</x-admin.button>
        </x-admin.empty-state>
    @else
        <div class="admin-mobile-cards md:hidden">
            @foreach ($wages as $wage)
                @php
                    $status = $wage->status instanceof \BackedEnum ? $wage->status->value : (string) $wage->status;
                    $pending = strcasecmp($status, 'Paid') !== 0;
                @endphp
                <article class="admin-mobile-card">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $wage->user?->name ?? '—' }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ number_format((float) $wage->hours_worked, 2) }}h · £{{ number_format((float) $wage->amount, 2) }}</p>
                        </div>
                        <span class="text-sm font-medium text-gray-600">{{ $status }}</span>
                    </div>
                    @if ($pending)
                        <form method="POST" action="{{ route('business-admin.payroll.paid', $wage) }}" class="mt-4">
                            @csrf
                            <x-admin.table-action type="submit" variant="success">Mark paid</x-admin.table-action>
                        </form>
                    @endif
                </article>
            @endforeach
        </div>

        <div class="admin-card hidden overflow-hidden md:block">
            <div class="admin-table-wrap">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
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
                    <tbody>
                        @foreach ($wages as $wage)
                            @php
                                $status = $wage->status instanceof \BackedEnum ? $wage->status->value : (string) $wage->status;
                                $pending = strcasecmp($status, 'Paid') !== 0;
                            @endphp
                            <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                                <td class="px-4 py-3.5 font-medium text-gray-900 dark:text-white">{{ $wage->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ number_format((float) $wage->hours_worked, 2) }}h</td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">£{{ number_format((float) $wage->amount, 2) }}</td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $status }}</td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $wage->paid_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $wage->notes ?: '—' }}</td>
                                <td class="px-4 py-3.5">
                                    @if ($pending)
                                        <form method="POST" action="{{ route('business-admin.payroll.paid', $wage) }}">
                                            @csrf
                                            <x-admin.table-action type="submit" variant="success">Mark paid</x-admin.table-action>
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

        <x-admin.pagination :paginator="$wages" />
    @endif

    <x-admin.modal name="add-wage" title="Add wage" max-width="max-w-md">
        <form method="POST" action="{{ route('business-admin.payroll.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="payroll-user" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Staff</label>
                <select id="payroll-user" name="user_id" required class="admin-input min-h-[44px] w-full">
                    @foreach ($staff as $member)
                        <option value="{{ $member->id }}" data-rate="{{ $member->hourly_rate ?? 0 }}">
                            {{ $member->name }}{{ $member->hourly_rate ? ' (£'.number_format((float) $member->hourly_rate, 2).'/h)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="admin-form-grid">
                <x-admin.input type="number" name="hours_worked" label="Hours worked" step="0.01" min="0" required class="w-full" />
                <x-admin.input type="number" name="hourly_rate" label="Hourly rate (£)" step="0.01" min="0" class="w-full" />
            </div>
            <x-admin.textarea name="notes" label="Notes" rows="3" class="w-full" />
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                <x-admin.button type="submit">Save wage</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
