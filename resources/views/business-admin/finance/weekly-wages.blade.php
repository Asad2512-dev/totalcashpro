<x-layouts.business-admin title="Weekly Wages" active="finance">
    <x-admin.toolbar title="Weekly Wages" description="Wages grouped by scheduled payment week.">
        <form method="GET" class="flex items-center gap-2">
            <input type="date" name="week" value="{{ $week_start->toDateString() }}" class="admin-input" />
            <x-admin.button type="submit" size="sm">Go</x-admin.button>
        </form>
    </x-admin.toolbar>
    <x-finance.nav active="weekly-wages" />

    <x-admin.stat-card class="mb-6" label="Total due this payment week" :value="'£'.number_format((float) $total, 2)" change="Week of {{ $week_start->format('d M Y') }}" tone="info" />

    @if ($wages->isEmpty())
        <x-admin.empty-state title="No wages for this payment week" description="Generate payroll from attendance or approve draft wages." />
    @else
        <div class="admin-card overflow-hidden">
            <div class="admin-table-wrap">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Staff</th>
                            <th class="px-4 py-3">Period</th>
                            <th class="px-4 py-3">Hours</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Payment due</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wages as $wage)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $wage->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $wage->period_start?->format('d M') ?? '—' }} – {{ $wage->period_end?->format('d M') ?? '—' }}</td>
                                <td class="px-4 py-3">{{ number_format((float) $wage->hours_worked, 2) }}h</td>
                                <td class="px-4 py-3">£{{ number_format((float) $wage->gross_amount, 2) }}</td>
                                <td class="px-4 py-3">{{ $wage->payment_due_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $wage->status instanceof \BackedEnum ? $wage->status->label() : $wage->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layouts.business-admin>
