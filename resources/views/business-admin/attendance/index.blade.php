@php
    $prevWeek = $from->copy()->subWeek()->toDateString();
    $nextWeek = $from->copy()->addWeek()->toDateString();
    $statusTone = static fn (string $status): string => match ($status) {
        'working', 'clocked_out' => 'success',
        'on_break' => 'info',
        'missing_clock_out' => 'warning',
        default => 'neutral',
    };
    $statusLabel = static fn (string $status): string => match ($status) {
        'working' => 'Working',
        'on_break' => 'On break',
        'clocked_out' => 'Clocked out',
        'missing_clock_out' => 'Missing clock out',
        default => ucfirst(str_replace('_', ' ', $status)),
    };
@endphp

<x-layouts.business-admin title="Attendance" active="attendance">
    <x-admin.mobile-page-header
        class="lg:hidden"
        title="Attendance"
        description="Clock times, breaks, paid hours and kiosk source."
    />

    <x-admin.toolbar title="Weekly attendance" description="Clock in / clock out times from live attendance logs, with hours and pay." class="hidden lg:flex">
        <x-admin.nav-pill :href="route('business-admin.attendance', ['week' => $prevWeek])">← Prev</x-admin.nav-pill>
        <form method="GET" class="inline-flex min-w-0 flex-1 items-center sm:flex-none">
            <x-admin.input type="date" name="week" :value="$weekStart" onchange="this.form.submit()" class="min-h-[44px] w-full min-w-[10rem]" />
        </form>
        <x-admin.nav-pill :href="route('business-admin.attendance', ['week' => $nextWeek])">Next →</x-admin.nav-pill>
    </x-admin.toolbar>

    <x-admin.filter-sheet title="Week" :active-count="1" class="mb-4 lg:hidden">
        <form method="GET" class="grid gap-3">
            <x-admin.input type="date" name="week" label="Week starting" :value="$weekStart" />
            <div class="admin-action-grid">
                <x-admin.action-tile :href="route('business-admin.attendance', ['week' => $prevWeek])" label="Previous week" />
                <x-admin.action-tile :href="route('business-admin.attendance', ['week' => $nextWeek])" label="Next week" />
            </div>
            <x-admin.button type="submit" size="sm">Apply</x-admin.button>
        </form>
    </x-admin.filter-sheet>

    <p class="mb-4 text-sm text-gray-500">{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</p>

    @if (! empty($sessions))
        <div class="mb-6">
            <h2 class="mb-3 font-display text-base font-bold text-gray-900 dark:text-white">Shift sessions</h2>

            <div class="admin-mobile-records mb-3 lg:hidden">
                @foreach ($sessions as $session)
                    <article class="admin-mobile-record">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="admin-mobile-record__title">{{ $session['user']->name }}</p>
                                <p class="mt-0.5 text-sm text-gray-500">{{ \Illuminate\Support\Carbon::parse($session['date'])->format('D j M') }} · {{ $session['branch']?->name ?? '—' }}</p>
                            </div>
                            <x-admin.badge :tone="$statusTone($session['status'])">{{ $statusLabel($session['status']) }}</x-admin.badge>
                        </div>
                        <dl class="mt-3 space-y-1">
                            <div class="admin-mobile-record__row">
                                <dt class="admin-mobile-record__label">Clock</dt>
                                <dd class="admin-mobile-record__value">{{ $session['clock_in'] }} – {{ $session['clock_out'] ?? '—' }}</dd>
                            </div>
                            <div class="admin-mobile-record__row">
                                <dt class="admin-mobile-record__label">Paid hours</dt>
                                <dd class="admin-mobile-record__value">{{ number_format($session['paid_hours'], 2) }}h</dd>
                            </div>
                            <div class="admin-mobile-record__row">
                                <dt class="admin-mobile-record__label">Breaks</dt>
                                <dd class="admin-mobile-record__value">{{ number_format($session['break_hours'], 2) }}h ({{ $session['breaks_count'] }})</dd>
                            </div>
                            @if ($session['kiosk'])
                                <div class="admin-mobile-record__row">
                                    <dt class="admin-mobile-record__label">Kiosk</dt>
                                    <dd class="admin-mobile-record__value">{{ $session['kiosk'] }}</dd>
                                </div>
                            @endif
                        </dl>
                        <div class="mt-3 border-t border-gray-100 pt-3 dark:border-gray-700">
                            <x-admin.button size="sm" variant="secondary" class="w-full" :href="route('business-admin.attendance.show', [$session['user']->id, $session['date']])">View detail</x-admin.button>
                        </div>
                    </article>
                @endforeach
            </div>

            <x-admin.card :padding="false" class="hidden lg:block">
                <div class="admin-table-wrap">
                    <table class="admin-table min-w-full text-left text-sm">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Employee</th>
                                <th class="px-4 py-3">Branch</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">In</th>
                                <th class="px-4 py-3">Out</th>
                                <th class="px-4 py-3">Gross</th>
                                <th class="px-4 py-3">Breaks</th>
                                <th class="px-4 py-3">Paid</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Kiosk</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sessions as $session)
                                <tr>
                                    <td class="px-4 py-3 font-medium">{{ $session['user']->name }}</td>
                                    <td class="px-4 py-3">{{ $session['branch']?->name ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ \Illuminate\Support\Carbon::parse($session['date'])->format('d M Y') }}</td>
                                    <td class="px-4 py-3">{{ $session['clock_in'] }}</td>
                                    <td class="px-4 py-3">{{ $session['clock_out'] ?? '—' }}</td>
                                    <td class="px-4 py-3">{{ number_format($session['gross_hours'], 2) }}h</td>
                                    <td class="px-4 py-3">{{ number_format($session['break_hours'], 2) }}h</td>
                                    <td class="px-4 py-3 font-semibold">{{ number_format($session['paid_hours'], 2) }}h</td>
                                    <td class="px-4 py-3"><x-admin.badge :tone="$statusTone($session['status'])">{{ $statusLabel($session['status']) }}</x-admin.badge></td>
                                    <td class="px-4 py-3 text-gray-500">{{ $session['kiosk'] ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <x-admin.table-action :href="route('business-admin.attendance.show', [$session['user']->id, $session['date']])" variant="neutral">Detail</x-admin.table-action>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-admin.card>
        </div>
    @endif

    @if (empty($report))
        <x-admin.empty-state title="No attendance data" description="Staff attendance appears here after PIN clock-ins." />
    @else
        <h2 class="mb-3 font-display text-base font-bold text-gray-900 dark:text-white">Weekly hours grid</h2>

        <div class="admin-mobile-cards md:hidden">
            @foreach ($report as $row)
                <article class="admin-mobile-card">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $row['user']->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ number_format($row['total_hours'], 2) }}h · £{{ number_format($row['total_pay'], 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-4 space-y-3">
                        @foreach ($row['days'] as $date => $dayData)
                            @if (! empty($dayData['slots']) || $dayData['hours'] > 0)
                                <div class="rounded-xl border border-gray-100 px-3 py-2.5 dark:border-gray-700">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ \Illuminate\Support\Carbon::parse($date)->format('D j M') }}</p>
                                    @if (! empty($dayData['slots']))
                                        <div class="mt-2 space-y-1.5">
                                            @foreach ($dayData['slots'] as $slot)
                                                <p class="text-sm text-gray-700 dark:text-gray-200">
                                                    <span class="font-medium text-primary-700">In {{ $slot['in'] }}</span>
                                                    · Out {{ $slot['out'] ?? '—' }}
                                                </p>
                                            @endforeach
                                        </div>
                                    @endif
                                    <p class="mt-1 text-xs font-medium text-gray-500">{{ number_format((float) $dayData['hours'], 2) }}h</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>

        <x-admin.card :padding="false" class="hidden md:block">
            <x-admin.matrix-wrap>
                <table>
                    <thead>
                        <tr>
                            <th class="admin-matrix-sticky px-4 py-3">Staff</th>
                            @foreach (array_keys($report[0]['days']) as $date)
                                <th class="min-w-[7.5rem] px-2 py-3 text-center">{{ \Illuminate\Support\Carbon::parse($date)->format('D j') }}</th>
                            @endforeach
                            <th class="px-3 py-3 text-right">Hours</th>
                            <th class="px-3 py-3 text-right">Pay</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report as $row)
                            <tr class="align-top">
                                <td class="admin-matrix-sticky px-4 py-3 font-medium text-gray-900 dark:text-white">
                                    {{ $row['user']->name }}
                                </td>
                                @foreach ($row['days'] as $dayData)
                                    <td class="px-2 py-3 text-center">
                                        @if (! empty($dayData['slots']))
                                            <div class="space-y-1.5">
                                                @foreach ($dayData['slots'] as $slot)
                                                    <div class="rounded-lg border border-primary-100 bg-primary-50/60 px-2 py-1.5 text-xs dark:border-primary-900/40 dark:bg-primary-900/20">
                                                        <div class="font-semibold text-primary-800 dark:text-primary-300">In {{ $slot['in'] }}</div>
                                                        <div class="font-semibold {{ $slot['out'] ? 'text-gray-700 dark:text-gray-200' : 'text-amber-600' }}">Out {{ $slot['out'] ?? '—' }}</div>
                                                    </div>
                                                @endforeach
                                                <div class="text-[11px] font-medium text-gray-500">{{ number_format((float) $dayData['hours'], 2) }}h</div>
                                            </div>
                                        @elseif ($dayData['hours'] > 0)
                                            <span class="text-primary-700">{{ number_format($dayData['hours'], 2) }}h</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-3 py-3 text-right font-semibold">{{ number_format($row['total_hours'], 2) }}h</td>
                                <td class="px-3 py-3 text-right font-semibold">£{{ number_format($row['total_pay'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-admin.matrix-wrap>
        </x-admin.card>
    @endif
</x-layouts.business-admin>
