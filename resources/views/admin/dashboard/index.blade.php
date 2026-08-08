<x-layouts.admin title="Dashboard" active="dashboard">
    <x-admin.toolbar description="Live metrics calculated from your TotalCashPro database.">
        <x-admin.button variant="secondary" size="sm" :href="route('super-admin.export', ['type' => 'businesses'])">Export</x-admin.button>
        <x-admin.button size="sm" href="{{ route('super-admin.businesses') }}">
            <x-admin.icon name="plus" class="h-4 w-4" /> View businesses
        </x-admin.button>
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

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <x-admin.chart-card
            title="Monthly revenue"
            description="Paid payments grouped by month."
            :bars="$revenueBars"
            empty="Revenue appears here after paid payments are recorded."
        />
        <x-admin.chart-card
            title="Subscription growth"
            description="New subscriptions created each month."
            :bars="$growthBars"
            empty="Subscription growth appears once organisations subscribe."
        />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div>
            <x-admin.toolbar section title="Recent businesses" description="Newest customer accounts." />
            @if (count($recentBusinesses) === 0)
                <x-admin.empty-state
                    title="No businesses yet"
                    description="Approved access requests and created organisations will appear here."
                />
            @else
                <x-admin.table
                    :columns="['Business', 'Owner', 'Plan', 'Status', 'Amount']"
                    :rows="collect($recentBusinesses)->map(fn ($row) => [
                        $row['business'],
                        $row['owner'],
                        $row['plan'],
                        $row['status'],
                        $row['amount'],
                    ])->all()"
                />
            @endif
        </div>

        <div class="admin-panel-grid">
            <x-admin.card>
                <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Recent activity</h3>
                @if (count($recentActivity) === 0)
                    <p class="mt-4 text-sm text-gray-500">No activity logged yet.</p>
                @else
                    <ul class="admin-compact-grid mt-4">
                        @foreach ($recentActivity as $item)
                            <li class="admin-compact-item">
                                <p class="admin-compact-item__title">{{ $item['action'] }}</p>
                                <p class="admin-compact-item__meta">{{ $item['actor'] }} · {{ $item['time'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>

            <x-admin.card>
                <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Latest payments</h3>
                @if (count($latestPayments) === 0)
                    <p class="mt-4 text-sm text-gray-500">No payments recorded yet.</p>
                @else
                    <ul class="admin-compact-grid mt-4">
                        @foreach ($latestPayments as $payment)
                            <li class="admin-compact-item">
                                <p class="admin-compact-item__title">{{ $payment['business'] }}</p>
                                <p class="admin-compact-item__meta">{{ $payment['invoice'] }}</p>
                                <p class="admin-compact-item__value">{{ $payment['amount'] }}</p>
                                <x-admin.badge :tone="$payment['status'] === 'Paid' ? 'success' : 'danger'" class="mt-1 w-fit text-[10px]">{{ $payment['status'] }}</x-admin.badge>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-admin.card>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <div>
            <x-admin.toolbar section title="Recent tickets" description="Latest support activity." />
            @if (count($recentTickets) === 0)
                <x-admin.empty-state
                    title="No support tickets"
                    description="Customer tickets will show here when submitted."
                />
            @else
                <x-admin.table
                    :columns="['Ticket', 'Business', 'Subject', 'Priority', 'Status']"
                    :rows="collect($recentTickets)->map(fn ($row) => [
                        $row['ticket'],
                        $row['business'],
                        $row['subject'],
                        $row['priority'],
                        $row['status'],
                    ])->all()"
                />
            @endif
        </div>
        <div>
            <x-admin.toolbar section title="Latest signups" description="Newest businesses entering the platform." />
            @if (count($recentBusinesses) === 0)
                <x-admin.empty-state
                    title="No signups yet"
                    description="New organisations will appear in this list."
                />
            @else
                <x-admin.card>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($recentBusinesses as $signup)
                            <li class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                                <div class="flex items-center gap-3">
                                    <x-admin.avatar :name="$signup['business']" />
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $signup['business'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $signup['owner'] }} · {{ $signup['plan'] }}</p>
                                    </div>
                                </div>
                                <x-admin.badge :tone="$signup['status'] === 'Active' ? 'success' : ($signup['status'] === 'Trial' ? 'info' : 'danger')">
                                    {{ $signup['status'] }}
                                </x-admin.badge>
                            </li>
                        @endforeach
                    </ul>
                </x-admin.card>
            @endif
        </div>
    </div>
</x-layouts.admin>
