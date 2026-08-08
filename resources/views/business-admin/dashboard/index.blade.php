<x-layouts.business-admin title="Dashboard" active="dashboard">
    <x-admin.mobile-page-header
        class="lg:hidden"
        title="Dashboard"
        :description="'Live totals for your organisation'.(auth()->user()->organization?->name ? ' · '.auth()->user()->organization->name : '').'.'"
    />

    <x-admin.toolbar
        class="hidden lg:flex"
        description="Live totals for your organisation{{ auth()->user()->organization?->name ? ' · '.auth()->user()->organization->name : '' }}."
    >
        <x-admin.button size="sm" :href="route('business-admin.cash-up')">
            <x-admin.icon name="cash" class="h-4 w-4" /> Cash Up
        </x-admin.button>
        <x-admin.button variant="secondary" size="sm" :href="route('business-admin.kiosk.settings')">Kiosk</x-admin.button>
    </x-admin.toolbar>

    @php
        $allStats = $stats;
    @endphp

    <x-admin.mobile-kpi-grid :items="$allStats" class="mb-4" />

    <div class="admin-stat-grid--compact mb-4 hidden lg:grid">
        @foreach ($stats as $stat)
            <x-admin.stat
                compact
                :label="$stat['label']"
                :value="$stat['value']"
                :change="$stat['change']"
                :tone="$stat['tone']"
            />
        @endforeach
    </div>

    <div class="admin-section-grid xl:grid-cols-[1.2fr_0.8fr]">
        <x-admin.chart-card
            :title="'Daily cash up · '.$cashChartPeriod"
            description="Net cash up for each day this month — matches the This month total above."
            :points="$cashChart"
            :total="$cashChartTotal"
            empty="Cash ups will appear here once recorded this month."
        />

        <x-admin.card>
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Quick actions</h3>
            <x-admin.action-grid class="mt-3">
                @foreach ($quickActions as $action)
                    <x-admin.action-tile
                        :href="route($action['route'])"
                        :icon="$action['icon']"
                        :label="$action['label']"
                        :variant="$action['variant'] ?? ($loop->first ? 'primary' : 'default')"
                    />
                @endforeach
            </x-admin.action-grid>
        </x-admin.card>
    </div>

    <div class="admin-section-grid">
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

        <div class="admin-panel-grid">
            <x-admin.card>
                <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Inventory alerts</h3>
                @if ($lowStock->isEmpty())
                    <p class="mt-3 text-sm text-gray-500">No low-stock items.</p>
                @else
                    <ul class="admin-compact-grid mt-3">
                        @foreach ($lowStock as $item)
                            <li class="admin-compact-item">
                                <p class="admin-compact-item__title">{{ $item->name }}</p>
                                <x-admin.badge tone="warning" class="mt-1 w-fit text-[10px]">{{ $item->stock_total_pcs }} pcs</x-admin.badge>
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
                    <ul class="admin-compact-grid mt-3">
                        @foreach ($upcomingPayments as $invoice)
                            <li class="admin-compact-item">
                                <p class="admin-compact-item__title">{{ $invoice->supplier?->name }}</p>
                                <p class="admin-compact-item__meta">Due {{ $invoice->due_date?->format('d M Y') }}</p>
                                <p class="admin-compact-item__value">£{{ number_format((float) $invoice->amount, 2) }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>
        </div>
    </div>
</x-layouts.business-admin>
