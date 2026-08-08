@php
    $prev = \Illuminate\Support\Carbon::parse($weekStart)->subWeek()->toDateString();
    $next = \Illuminate\Support\Carbon::parse($weekStart)->addWeek()->toDateString();
    $state = $attendanceState['state'] ?? 'not_checked_in';
    $stateLabel = match ($state) {
        'checked_in' => 'Currently Working',
        'on_break' => 'On Break',
        default => 'Clock In',
    };
@endphp

<x-layouts.staff title="My Rota" active="shift">
    <x-admin.mobile-page-header
        class="lg:hidden"
        title="My Rota"
        :description="$weekLabel"
    >
        <x-slot:actions>
            <x-admin.button size="sm" variant="secondary" :href="route('staff.shift.print', ['week' => $weekStart])" target="_blank">Print</x-admin.button>
        </x-slot:actions>
    </x-admin.mobile-page-header>

    <x-admin.toolbar title="My Rota" :description="$weekLabel" class="hidden lg:flex">
        <x-admin.nav-pill :href="route('staff.shift', ['week' => $prev])">← Prev</x-admin.nav-pill>
        <x-admin.nav-pill :href="route('staff.shift', ['week' => $next])">Next →</x-admin.nav-pill>
        <x-admin.button size="sm" variant="secondary" :href="route('staff.shift.print', ['week' => $weekStart])" target="_blank">Print My Rota</x-admin.button>
    </x-admin.toolbar>

    <div class="mb-4 flex gap-2 lg:hidden">
        <x-admin.nav-pill :href="route('staff.shift', ['week' => $prev])">← Prev</x-admin.nav-pill>
        <x-admin.nav-pill :href="route('staff.shift', ['week' => $next])">Next →</x-admin.nav-pill>
    </div>

    @if ($todayShift)
        <x-admin.card class="mb-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-primary-700">Today's Shift</p>
            <p class="mt-1 font-display text-2xl font-bold text-gray-900 dark:text-white">{{ $todayShift['time'] }}</p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                {{ $todayShift['section'] ?? 'Section' }}
                @if ($todayShift['group']) · {{ $todayShift['group'] }} @endif
                · {{ $todayShift['branch'] ?? $summary['branch'] }}
            </p>
            <p class="mt-1 text-sm text-gray-500">
                {{ $todayShift['hours'] }}h scheduled
                @if ($todayShift['break_minutes'])
                    · Break {{ $todayShift['break_minutes'] }}m
                @endif
            </p>
            <div class="mt-4">
                <x-admin.badge :tone="in_array($state, ['checked_in', 'on_break'], true) ? 'success' : 'neutral'">{{ $stateLabel }}</x-admin.badge>
            </div>
        </x-admin.card>
    @endif

    <div class="admin-panel-grid mb-4">
        <x-admin.card>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Scheduled hours</p>
            <p class="mt-1 font-display text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['scheduled_hours'] }}h</p>
        </x-admin.card>
        <x-admin.card>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Shifts</p>
            <p class="mt-1 font-display text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['shift_count'] }}</p>
        </x-admin.card>
    </div>

    @if (empty($days) || collect($days)->every(fn ($d) => $d['off']))
        <x-admin.empty-state title="No published rota" description="Your manager has not published the rota for this week yet." />
    @else
        <div class="admin-mobile-records lg:hidden">
            @foreach ($days as $day)
                <article class="admin-mobile-record">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="admin-mobile-record__title">{{ $day['label'] }}</p>
                            <p class="mt-0.5 text-sm text-gray-500">{{ $day['short'] }}</p>
                        </div>
                        @if ($day['is_today'])
                            <x-admin.badge tone="info">Today</x-admin.badge>
                        @elseif ($day['is_tomorrow'])
                            <x-admin.badge tone="neutral">Tomorrow</x-admin.badge>
                        @endif
                    </div>
                    @if ($day['off'])
                        <p class="mt-3 text-sm font-semibold text-gray-400">OFF</p>
                    @else
                        @foreach ($day['shifts'] as $shift)
                            <div class="mt-3 rounded-xl border border-gray-200 p-3 dark:border-gray-700">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $shift['time'] }}</p>
                                <p class="mt-1 text-sm text-gray-500">{{ $shift['section'] ?? 'Section' }} · {{ $shift['branch'] ?? $summary['branch'] }}</p>
                                @if ($shift['break_minutes'])
                                    <p class="mt-1 text-xs text-gray-500">Break: {{ $shift['break_minutes'] }} min</p>
                                @endif
                                <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-primary-700">{{ $shift['status'] }}</p>
                            </div>
                        @endforeach
                    @endif
                </article>
            @endforeach
        </div>

        @php
            $tableRows = collect($days)->flatMap(function ($day) use ($summary) {
                if ($day['off']) {
                    return [[$day['label'].' · '.$day['short'], 'OFF', '—', $summary['branch'] ?? '—', '0', '—']];
                }

                return collect($day['shifts'])->map(fn ($shift) => [
                    $day['label'].' · '.$day['short'],
                    $shift['time'],
                    trim(($shift['section'] ?? '—').($shift['group'] ? ' · '.$shift['group'] : '')),
                    $shift['branch'] ?? ($summary['branch'] ?? '—'),
                    $shift['hours'].'h',
                    $shift['status'],
                ]);
            })->all();
        @endphp
        <x-admin.card :padding="false" class="hidden lg:block">
            <x-admin.table
                :columns="['Day', 'Shift', 'Section / Group', 'Branch', 'Hours', 'Status']"
                :rows="$tableRows"
            />
        </x-admin.card>
    @endif
</x-layouts.staff>
