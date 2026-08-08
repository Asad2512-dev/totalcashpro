<x-layouts.business-admin title="Dashboard" active="dashboard">
    <x-admin.toolbar description="Live metrics and shortcuts for {{ auth()->user()->organization?->name ?? 'your business' }}." />

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

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <x-admin.chart-card
            :title="'Daily cash up · '.($cashChartPeriod ?? now()->format('F Y'))"
            description="Net cash up for each day this month."
            :points="$cashChart"
            :total="$cashChartTotal"
        />

        <x-admin.card title="Quick actions">
            <x-admin.action-grid>
                @foreach ($quickActions as $action)
                    <x-admin.action-tile
                        :href="route($action['route'])"
                        :icon="$action['icon']"
                        :label="$action['label']"
                        :variant="$loop->first ? 'primary' : 'default'"
                    />
                @endforeach
            </x-admin.action-grid>
        </x-admin.card>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div>
            <x-admin.toolbar section title="Recent cash ups" description="Latest completed cash ups for this branch." />
            @if ($recentCashUps->isEmpty())
                <x-admin.empty-state
                    icon="cash"
                    title="No cash ups yet"
                    description="Record your first cash up to see it here."
                />
            @else
                <x-admin.table>
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="hidden px-4 py-3 sm:table-cell">Shift</th>
                            <th class="px-4 py-3 text-right">Net total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentCashUps as $cashUp)
                            <tr>
                                <td class="admin-table-stack-title px-4 py-3.5" data-label="Date">{{ $cashUp->cashup_date }}</td>
                                <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Shift">
                                    <x-admin.badge>{{ ucfirst($cashUp->shift) }}</x-admin.badge>
                                </td>
                                <td class="px-4 py-3.5 text-right font-semibold" data-label="Net total">£{{ number_format($cashUp->netTotal(), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-admin.table>
            @endif
        </div>

        <x-admin.card>
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Low stock alerts</h3>
            @if ($lowStock->isEmpty())
                <p class="mt-4 text-sm text-gray-500">All stock levels look good.</p>
            @else
                <ul class="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($lowStock as $item)
                        <li class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->category?->name }}</p>
                            </div>
                            <x-admin.badge tone="warning">{{ $item->latestCount?->quantity ?? 0 }} {{ $item->unit }}</x-admin.badge>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.card>
    </div>

    @if ($upcomingPayments->isNotEmpty())
        <div class="mt-6">
            <x-admin.toolbar section title="Upcoming supplier payments" description="Invoices due soon." />
            <x-admin.table>
                <thead>
                    <tr>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="hidden px-4 py-3 md:table-cell">Invoice</th>
                        <th class="hidden px-4 py-3 sm:table-cell">Due date</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($upcomingPayments as $payment)
                        <tr>
                            <td class="admin-table-stack-title px-4 py-3.5" data-label="Supplier">{{ $payment->supplier?->name }}</td>
                            <td class="hidden px-4 py-3.5 md:table-cell" data-label="Invoice">{{ $payment->invoice_number }}</td>
                            <td class="hidden px-4 py-3.5 sm:table-cell" data-label="Due date">{{ $payment->due_date }}</td>
                            <td class="px-4 py-3.5 text-right font-semibold" data-label="Amount">£{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-admin.table>
        </div>
    @endif

    @if (! empty($recentActivity))
        <x-admin.card class="mt-6">
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Recent activity</h3>
            <ul class="mt-4 space-y-4">
                @foreach ($recentActivity as $activity)
                    <li class="flex gap-3">
                        <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-primary-500"></span>
                        <div class="min-w-0">
                            <p class="text-sm text-gray-800 dark:text-gray-100">{{ $activity['action'] }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $activity['actor'] }} · {{ $activity['time'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-admin.card>
    @endif
</x-layouts.business-admin>
