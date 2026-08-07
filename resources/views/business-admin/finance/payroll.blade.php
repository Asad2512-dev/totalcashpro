<x-layouts.business-admin title="Payroll" active="finance">
    <x-admin.toolbar title="Payroll" description="Draft → approve → pay workflow with attendance generation.">
        @foreach (['current' => 'All', 'draft' => 'Draft', 'approved' => 'Approved', 'paid' => 'Paid'] as $key => $label)
            <x-admin.nav-pill :href="route('business-admin.finance.payroll', ['period' => $key])" :active="$period === $key">{{ $label }}</x-admin.nav-pill>
        @endforeach
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-wage')">Add wage</x-admin.button>
        <x-admin.button size="sm" variant="secondary" x-data @click="$dispatch('open-modal', 'generate-payroll')">From attendance</x-admin.button>
    </x-admin.toolbar>
    <x-finance.nav active="payroll" />

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-admin.stat-card label="Draft total" :value="'£'.number_format((float) ($summary['draft_total'] ?? 0), 2)" tone="neutral" />
        <x-admin.stat-card label="Approved total" :value="'£'.number_format((float) ($summary['approved_total'] ?? 0), 2)" tone="warning" />
        <x-admin.stat-card label="Paid this month" :value="'£'.number_format((float) ($summary['paid_month_total'] ?? 0), 2)" tone="success" />
    </div>

    @if ($wages->isEmpty())
        <x-admin.empty-state title="No wage records" description="Generate from attendance or add manual entries." />
    @else
        <div class="admin-card overflow-hidden">
            <div class="admin-table-wrap">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Staff</th>
                            <th class="px-4 py-3">Hours</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Due</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wages as $wage)
                            @php $status = $wage->status instanceof \BackedEnum ? $wage->status->value : (string) $wage->status; @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $wage->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ number_format((float) $wage->hours_worked, 2) }}h</td>
                                <td class="px-4 py-3">£{{ number_format((float) $wage->gross_amount, 2) }}</td>
                                <td class="px-4 py-3">{{ $wage->payment_due_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $wage->status instanceof \BackedEnum ? $wage->status->label() : $status }}</td>
                                <td class="px-4 py-3">
                                    @if (strcasecmp($status, 'Draft') === 0)
                                        <form method="POST" action="{{ route('business-admin.finance.payroll.approve-wage', $wage) }}" class="inline">@csrf<button class="text-primary-700 font-semibold">Approve</button></form>
                                    @elseif (in_array($status, ['Approved', 'Pending'], true))
                                        <form method="POST" action="{{ route('business-admin.finance.payroll.paid', $wage) }}" class="inline">@csrf<button class="text-primary-700 font-semibold">Mark paid</button></form>
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

    @if (($payroll_runs ?? collect())->isNotEmpty())
        <section class="admin-card mt-6 p-5">
            <h2 class="font-semibold text-gray-900 dark:text-white">Recent payroll runs</h2>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($payroll_runs as $run)
                    <li class="flex items-center justify-between rounded border border-gray-200 px-3 py-2 dark:border-gray-700">
                        <span>Week {{ $run->week_start->format('d M') }} – {{ $run->week_end->format('d M Y') }} · Pay {{ $run->payment_due_date->format('d M Y') }}</span>
                        @if (($run->status?->value ?? '') === 'draft')
                            <form method="POST" action="{{ route('business-admin.finance.payroll.approve-run', $run) }}">@csrf<button class="text-primary-700 font-semibold">Approve run</button></form>
                        @else
                            <span class="capitalize text-gray-500">{{ $run->status?->label() ?? $run->status }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <x-admin.modal name="add-wage" title="Add wage">
        <form method="POST" action="{{ route('business-admin.finance.payroll.store') }}" class="space-y-4">
            @csrf
            <label class="block text-sm font-medium">Staff
                <select name="user_id" class="admin-input mt-1 w-full" required>
                    @foreach ($staff as $member)
                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                    @endforeach
                </select>
            </label>
            <x-admin.form-field label="Hours worked" name="hours_worked" type="number" step="0.01" required />
            <x-admin.form-field label="Notes" name="notes" type="textarea" />
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="submit">Save draft</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>

    <x-admin.modal name="generate-payroll" title="Generate from attendance">
        <form method="POST" action="{{ route('business-admin.finance.payroll.generate') }}" class="space-y-4">
            @csrf
            <x-admin.form-field label="Week starting" name="week_start" type="date" :value="now()->startOfWeek()->toDateString()" />
            <x-admin.form-field label="Payment due date" name="payment_due_date" type="date" :value="now()->startOfWeek()->addWeek()->endOfWeek()->toDateString()" />
            <p class="text-sm text-gray-500">Work completed in the selected week can be scheduled for payment on a later date.</p>
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="submit">Generate draft run</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
