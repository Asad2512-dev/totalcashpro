<x-layouts.business-admin title="Reports" active="reports">
    <x-admin.toolbar title="Business reports" description="Live cash, wages, suppliers and stock for the selected period.">
        @foreach (['daily', 'weekly', 'monthly'] as $p)
            <a
                href="{{ route('business-admin.reports', ['period' => $p]) }}"
                @class([
                    'rounded-xl px-3 py-2 text-sm font-semibold',
                    'bg-primary-600 text-white' => $period === $p,
                    'border border-gray-200 text-gray-700 dark:border-gray-700 dark:text-gray-200' => $period !== $p,
                ])
            >{{ ucfirst($p) }}</a>
        @endforeach
        <form method="GET" class="inline-flex flex-wrap items-center gap-2">
            <input type="hidden" name="period" value="custom">
            <x-admin.input type="date" name="from" :value="$from" />
            <x-admin.input type="date" name="to" :value="$to" />
            <x-admin.button type="submit" size="sm" variant="secondary">Apply</x-admin.button>
        </form>
    </x-admin.toolbar>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat label="Total revenue" :value="'£'.number_format((float) $totalRevenue, 2)" change="Net cash ups" tone="success" />
        <x-admin.stat label="Total wages" :value="'£'.number_format((float) $totalWages, 2)" change="Wage ledger" tone="info" />
        <x-admin.stat label="Pending suppliers" :value="'£'.number_format((float) $totalSupplierInvoices, 2)" change="Outstanding" tone="warning" />
        <x-admin.stat
            label="Est. profit"
            :value="'£'.number_format((float) $profit, 2)"
            :change="$profit >= 0 ? 'Positive' : 'Negative'"
            :tone="$profit >= 0 ? 'success' : 'danger'"
        />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <x-admin.chart-card
            title="Revenue trend"
            description="Daily net cash for the selected range."
            :bars="$chartBars"
            empty="No cash ups in this period yet."
        />
        <x-admin.card>
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Operations</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-500">Active staff</dt>
                    <dd class="font-semibold">{{ $activeStaffCount }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-500">Low stock alerts</dt>
                    <dd class="font-semibold">{{ $lowStockCount }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-500">Paid wages</dt>
                    <dd class="font-semibold">£{{ number_format((float) $paidWages, 2) }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-500">Range</dt>
                    <dd class="font-semibold">{{ \Illuminate\Support\Carbon::parse($from)->format('d M') }} – {{ \Illuminate\Support\Carbon::parse($to)->format('d M Y') }}</dd>
                </div>
            </dl>
        </x-admin.card>
    </div>

    <div class="mt-6">
        <x-admin.toolbar title="Cash ups in period" description="Source data for revenue above." />
        @if ($cashRows->isEmpty())
            <x-admin.empty-state title="No cash ups" description="Record cash ups to populate this report." />
        @else
            <x-admin.table
                :columns="['Date', 'Shift', 'Branch', 'Net']"
                :rows="$cashRows->map(fn ($row) => [
                    $row->cashup_date?->format('d M Y'),
                    $row->shift instanceof \BackedEnum ? $row->shift->value : (string) $row->shift,
                    $row->branch?->name ?? '—',
                    '£'.number_format($row->netTotal(), 2),
                ])->all()"
            />
        @endif
    </div>
</x-layouts.business-admin>
