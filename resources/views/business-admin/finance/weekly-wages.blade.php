<x-layouts.business-admin title="Weekly Wages" active="finance">
    <x-finance.page-header title="Weekly Wages" description="Wages grouped by scheduled payment week.">
        <form method="GET" class="hidden items-center gap-2 lg:flex">
            <input type="date" name="week" value="{{ $week_start->toDateString() }}" class="admin-input" />
            <x-admin.button type="submit" size="sm">Go</x-admin.button>
        </form>
    </x-finance.page-header>
    <x-finance.nav active="weekly-wages" />

    <x-admin.filter-sheet title="Payment week" :active-count="1" class="mb-4 lg:hidden">
        <form method="GET" class="grid gap-4">
            <x-admin.input type="date" name="week" label="Week starting" :value="$week_start->toDateString()" />
            <x-admin.button type="submit" size="sm">Apply</x-admin.button>
        </form>
    </x-admin.filter-sheet>

    @php
        $weekKpis = [
            ['label' => 'Total due', 'value' => '£'.number_format((float) $total, 2), 'change' => 'Week of '.$week_start->format('d M Y'), 'tone' => 'info'],
        ];
    @endphp

    <x-admin.mobile-kpi-grid :items="$weekKpis" class="mb-4 lg:hidden" />

    <x-admin.stat-card class="mb-6 hidden lg:block" label="Total due this payment week" :value="'£'.number_format((float) $total, 2)" change="Week of {{ $week_start->format('d M Y') }}" tone="info" />

    @if ($wages->isEmpty())
        <x-admin.empty-state title="No wages for this payment week" description="Generate payroll from attendance or approve draft wages." />
    @else
        <x-admin.table-shell sticky>
            <thead>
                <tr>
                    <th class="px-4 py-3">Staff</th>
                    <th class="hidden px-4 py-3 md:table-cell">Period</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Hours</th>
                    <th class="px-4 py-3">Amount</th>
                    <th class="hidden px-4 py-3 lg:table-cell">Payment due</th>
                    <th class="hidden px-4 py-3 sm:table-cell">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($wages as $wage)
                    <tr>
                        <td class="admin-table-stack-title px-4 py-3.5 font-medium" data-label="Staff">{{ $wage->user?->name ?? '—' }}</td>
                        <td class="hidden px-4 py-3.5 md:table-cell" data-label="Period">{{ $wage->period_start?->format('d M') ?? '—' }} – {{ $wage->period_end?->format('d M') ?? '—' }}</td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Hours">{{ number_format((float) $wage->hours_worked, 2) }}h</td>
                        <td class="px-4 py-3.5" data-label="Amount">£{{ number_format((float) $wage->gross_amount, 2) }}</td>
                        <td class="hidden px-4 py-3.5 lg:table-cell" data-label="Payment due">{{ $wage->payment_due_date?->format('d M Y') ?? '—' }}</td>
                        <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Status">{{ $wage->status instanceof \BackedEnum ? $wage->status->label() : $wage->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-admin.table-shell>
    @endif
</x-layouts.business-admin>
