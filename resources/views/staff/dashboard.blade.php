<x-layouts.staff title="Dashboard" active="dashboard">
    <x-admin.toolbar title="Staff Dashboard" description="Your shift, clock status and today’s work at a glance." />

    <div class="admin-stat-grid">
        <x-admin.card>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Assigned branch</p>
            <p class="mt-2 font-display text-xl font-bold text-gray-900 dark:text-white">{{ $branchName }}</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Clock status</p>
            <p class="mt-2 font-display text-xl font-bold text-primary-700">{{ str_replace('_', ' ', ucfirst($state['state'])) }}</p>
            @if ($state['hours'] !== null)
                <p class="mt-1 text-sm text-gray-500">{{ number_format((float) $state['hours'], 2) }}h today</p>
            @endif
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Today’s shift</p>
            @if ($todayShift)
                <p class="mt-2 font-display text-xl font-bold text-gray-900 dark:text-white">{{ $todayShift->shift_type }}</p>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $todayShift->start_time?->format('H:i') }} – {{ $todayShift->end_time?->format('H:i') }}
                    @if ($todayShift->rotaSection)
                        · {{ $todayShift->rotaSection->name }}
                    @endif
                </p>
            @else
                <p class="mt-2 font-display text-xl font-bold text-gray-900 dark:text-white">No shift</p>
                <p class="mt-1 text-sm text-gray-500">Nothing assigned for today</p>
            @endif
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">This week</p>
            <p class="mt-2 font-display text-xl font-bold text-gray-900 dark:text-white">{{ number_format($weeklyHours ?? 0, 2) }}h</p>
            <a href="{{ route('staff.hours') }}" class="mt-1 inline-block text-sm font-semibold text-primary-700">View hours →</a>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Quick actions</p>
            <div class="mt-3 flex flex-wrap gap-2">
                <a href="{{ route('staff.clock') }}" class="admin-touch-target inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white">Clock</a>
                <a href="{{ route('staff.cash-up') }}" class="admin-touch-target inline-flex items-center justify-center rounded-xl border border-gray-200 px-4 text-sm font-semibold dark:border-gray-700">Cash Up</a>
                <a href="{{ route('staff.attendance') }}" class="admin-touch-target inline-flex items-center justify-center rounded-xl border border-gray-200 px-4 text-sm font-semibold dark:border-gray-700">Attendance</a>
            </div>
        </x-admin.card>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-admin.card>
            <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Recent cash ups</h3>
            <div class="mt-4 space-y-3">
                @forelse ($recentCashUps as $cashUp)
                    <div class="flex items-center justify-between rounded-xl border border-gray-100 px-3 py-2.5 dark:border-gray-700">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $cashUp->cashup_date?->format('d M Y') }} · {{ $cashUp->shift instanceof \BackedEnum ? $cashUp->shift->value : $cashUp->shift }}</p>
                            <p class="text-xs text-gray-500">Net £{{ number_format($cashUp->netTotal(), 2) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No cash ups for your branch yet.</p>
                @endforelse
            </div>
        </x-admin.card>

        <x-admin.card>
            <div class="flex items-center justify-between">
                <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Notifications</h3>
                <a href="{{ route('staff.notifications') }}" class="text-sm font-semibold text-primary-700">View all</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($notifications as $notification)
                    <div class="rounded-xl border border-gray-100 px-3 py-2.5 dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $notification->title ?? 'Notification' }}</p>
                        <p class="mt-0.5 text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($notification->body ?? $notification->message ?? '', 80) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">You’re all caught up.</p>
                @endforelse
            </div>
        </x-admin.card>
    </div>
</x-layouts.staff>
