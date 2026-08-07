<x-layouts.business-admin title="Dashboard" active="dashboard">
    <x-admin.toolbar
        description="Live totals for your organisation{{ auth()->user()->organization?->name ? ' · '.auth()->user()->organization->name : '' }}."
    >
        <x-admin.button size="sm" :href="route('business-admin.cash-up')">
            <x-admin.icon name="cash" class="h-4 w-4" /> Cash Up
        </x-admin.button>
        <x-admin.button variant="secondary" size="sm" :href="route('business-admin.kiosks.index')">Smart Kiosks</x-admin.button>
    </x-admin.toolbar>

    <div class="admin-stat-grid">
        @foreach ($stats as $stat)
            <x-admin.stat
                :label="$stat['label']"
                :value="$stat['value']"
                :change="$stat['change']"
                :tone="$stat['tone']"
            />
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <x-admin.chart-card
            :title="'Daily cash up · '.$cashChartPeriod"
            description="Net cash up for each day this month — matches the This month total above."
            :points="$cashChart"
            :total="$cashChartTotal"
            empty="Cash ups will appear here once recorded this month."
        />

        <x-admin.card>
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Quick actions</h3>
            <div class="mt-4 grid gap-2">
                @foreach ($quickActions as $action)
                    <a href="{{ route($action['route']) }}" class="admin-touch-target flex items-center gap-3 rounded-xl border border-gray-200 px-3 py-3 text-sm font-medium text-gray-700 transition hover:border-primary-300 hover:bg-primary-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-primary-900/20">
                        <x-admin.icon :name="$action['icon']" class="h-4 w-4 text-primary-600" />
                        {{ $action['label'] }}
                    </a>
                @endforeach
            </div>
        </x-admin.card>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div>
            <x-admin.toolbar section title="Recent cash ups" description="Latest saved shifts." />
            @if ($recentCashUps->isEmpty())
                <x-admin.empty-state title="No cash ups yet" description="Record your first Morning or Evening cash up." />
            @else
                <x-admin.table
                    :columns="['Date', 'Shift', 'Branch', 'Net']"
                    :rows="$recentCashUps->map(fn ($row) => [
                        $row->cashup_date?->format('d M Y'),
                        $row->shift?->value ?? $row->shift,
                        $row->branch?->name ?? '—',
                        '£'.number_format($row->netTotal(), 2),
                    ])->all()"
                />
            @endif
        </div>

        <div class="space-y-6">
            <x-admin.card>
                <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Inventory alerts</h3>
                @if ($lowStock->isEmpty())
                    <p class="mt-3 text-sm text-gray-500">No low-stock items.</p>
                @else
                    <ul class="mt-3 space-y-2">
                        @foreach ($lowStock as $item)
                            <li class="flex items-center justify-between text-sm">
                                <span>{{ $item->name }}</span>
                                <x-admin.badge tone="warning">{{ $item->stock_total_pcs }} pcs</x-admin.badge>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>

            <x-admin.card>
                <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Upcoming supplier payments</h3>
                @if ($upcomingPayments->isEmpty())
                    <p class="mt-3 text-sm text-gray-500">No upcoming invoices.</p>
                @else
                    <ul class="mt-3 space-y-3">
                        @foreach ($upcomingPayments as $invoice)
                            <li class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="font-medium">{{ $invoice->supplier?->name }}</p>
                                    <p class="text-xs text-gray-500">Due {{ $invoice->due_date?->format('d M Y') }}</p>
                                </div>
                                <span class="font-semibold">£{{ number_format((float) $invoice->amount, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>
        </div>
    </div>
</x-layouts.business-admin>
