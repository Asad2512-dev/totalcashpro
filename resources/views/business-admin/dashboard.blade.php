<x-layouts.business-admin title="Dashboard" active="dashboard">
    <div class="space-y-6">
        {{-- Stats Grid --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($stats as $stat)
                <x-admin.stat-card
                    :label="$stat['label']"
                    :value="$stat['value']"
                    :change="$stat['change']"
                    :tone="$stat['tone']"
                />
            @endforeach
        </div>

        {{-- Revenue Chart + Quick Actions --}}
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-admin.chart-card
                    :title="'Daily cash up · '.($cashChartPeriod ?? now()->format('F Y'))"
                    description="Net cash up for each day this month."
                    :points="$cashChart"
                    :total="$cashChartTotal"
                />
            </div>

            <x-admin.card title="Quick Actions">
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($quickActions as $action)
                        <a
                            href="{{ route($action['route']) }}"
                            class="flex items-center gap-3 px-4 py-3 transition hover:bg-gray-50 dark:hover:bg-gray-800"
                        >
                            <x-admin.icon :name="$action['icon']" class="h-5 w-5 text-primary-600" />
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $action['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </x-admin.card>
        </div>

        {{-- Recent Activity Grid --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Recent Cash Ups --}}
            <x-admin.card title="Recent Cash Ups">
                @if ($recentCashUps->isEmpty())
                    <x-admin.empty-state
                        icon="cash"
                        title="No cash ups yet"
                        description="Record your first cash up to see it here"
                    />
                @else
                    <x-admin.table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shift</th>
                                <th class="text-right">Net Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentCashUps as $cashUp)
                                <tr>
                                    <td>{{ $cashUp->cashup_date }}</td>
                                    <td><x-admin.badge>{{ ucfirst($cashUp->shift) }}</x-admin.badge></td>
                                    <td class="text-right font-semibold">£{{ number_format($cashUp->netTotal(), 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-admin.table>
                @endif
            </x-admin.card>

            {{-- Low Stock Alerts --}}
            <x-admin.card title="Low Stock Alerts">
                @if ($lowStock->isEmpty())
                    <x-admin.empty-state
                        icon="tag"
                        title="All stock levels good"
                        description="No items are running low"
                    />
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($lowStock as $item)
                            <div class="flex items-center justify-between px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->category?->name }}</p>
                                </div>
                                <x-admin.badge tone="warning">{{ $item->latestCount?->quantity ?? 0 }} {{ $item->unit }}</x-admin.badge>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-admin.card>
        </div>

        {{-- Upcoming Payments --}}
        @if ($upcomingPayments->isNotEmpty())
            <x-admin.card title="Upcoming Supplier Payments">
                <x-admin.table>
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Invoice</th>
                            <th>Due Date</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($upcomingPayments as $payment)
                            <tr>
                                <td>{{ $payment->supplier?->name }}</td>
                                <td>{{ $payment->invoice_number }}</td>
                                <td>{{ $payment->due_date }}</td>
                                <td class="text-right font-semibold">£{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-admin.table>
            </x-admin.card>
        @endif

        {{-- Recent Activity --}}
        @if (!empty($recentActivity))
            <x-admin.card title="Recent Activity">
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($recentActivity as $activity)
                        <div class="flex items-center justify-between px-4 py-3">
                            <div>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $activity['action'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['actor'] }}</p>
                            </div>
                            <span class="text-xs text-gray-500">{{ $activity['time'] }}</span>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>
        @endif
    </div>
</x-layouts.business-admin>
