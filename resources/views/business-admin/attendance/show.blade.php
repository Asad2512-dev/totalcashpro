@php
    $statusTone = static fn (string $status): string => match ($status) {
        'working', 'clocked_out' => 'success',
        'on_break' => 'info',
        'missing_clock_out' => 'warning',
        default => 'neutral',
    };
@endphp

<x-layouts.business-admin :title="$user->name.' · Attendance'" active="attendance">
    <x-admin.mobile-page-header
        class="lg:hidden"
        :title="$user->name"
        :description="$date->format('l j F Y')"
    >
        <x-slot:actions>
            <x-admin.button size="sm" variant="secondary" :href="route('business-admin.attendance', ['week' => $date->copy()->startOfWeek()->toDateString()])">Back</x-admin.button>
        </x-slot:actions>
    </x-admin.mobile-page-header>

    <x-admin.toolbar :title="$user->name" :description="$date->format('l j F Y')" class="hidden lg:flex">
        <x-admin.button size="sm" variant="secondary" :href="route('business-admin.attendance', ['week' => $date->copy()->startOfWeek()->toDateString()])">Back to attendance</x-admin.button>
    </x-admin.toolbar>

    <div class="admin-panel-grid mb-4">
        <x-admin.card>
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Employee</h3>
            <dl class="admin-compact-grid mt-3 text-sm">
                <div class="admin-compact-item">
                    <dt class="admin-compact-item__meta">Name</dt>
                    <dd class="admin-compact-item__title">{{ $user->name }}</dd>
                </div>
                <div class="admin-compact-item">
                    <dt class="admin-compact-item__meta">Branch</dt>
                    <dd class="admin-compact-item__title">{{ $user->branch?->name ?? '—' }}</dd>
                </div>
            </dl>
        </x-admin.card>

        <x-admin.card>
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Published shift</h3>
            @if ($shift)
                <dl class="admin-compact-grid mt-3 text-sm">
                    <div class="admin-compact-item">
                        <dt class="admin-compact-item__meta">Start</dt>
                        <dd class="admin-compact-item__value">{{ $shift->start_time?->format('H:i') ?? '—' }}</dd>
                    </div>
                    <div class="admin-compact-item">
                        <dt class="admin-compact-item__meta">End</dt>
                        <dd class="admin-compact-item__value">{{ $shift->end_time?->format('H:i') ?? '—' }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-3 text-sm text-gray-500">No published rota shift for this date.</p>
            @endif
        </x-admin.card>
    </div>

    @if ($sessions === [])
        <x-admin.empty-state title="No sessions" description="No clock-in records for this day." />
    @else
        @foreach ($sessions as $session)
            <x-admin.card class="mb-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">
                            {{ $session['clock_in'] }} – {{ $session['clock_out'] ?? 'Open' }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Gross {{ number_format($session['gross_hours'], 2) }}h ·
                            Breaks {{ number_format($session['break_hours'], 2) }}h ·
                            Paid {{ number_format($session['paid_hours'], 2) }}h
                        </p>
                    </div>
                    <x-admin.badge :tone="$statusTone($session['status'])">{{ ucfirst(str_replace('_', ' ', $session['status'])) }}</x-admin.badge>
                </div>

                <dl class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
                    <div><span class="text-gray-500">Source</span> <span class="font-medium capitalize">{{ $session['source'] }}</span></div>
                    <div><span class="text-gray-500">Kiosk</span> <span class="font-medium">{{ $session['kiosk'] ?? '—' }}</span></div>
                </dl>

                @if ($breaks->isNotEmpty())
                    <h4 class="mt-5 text-sm font-semibold text-gray-900 dark:text-white">Break timeline</h4>
                    <ol class="mt-3 space-y-2">
                        @foreach ($breaks as $break)
                            <li class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm dark:border-gray-700">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="font-medium capitalize">{{ $break->break_type?->label() ?? $break->break_type }}</span>
                                    <span class="text-gray-500">
                                        {{ $break->break_started_at?->format('H:i') }} –
                                        {{ $break->break_ended_at?->format('H:i') ?? 'Active' }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $break->is_paid ? 'Paid' : 'Unpaid' }}
                                    @if ($break->branchKiosk)
                                        · {{ $break->branchKiosk->name }}
                                    @endif
                                </p>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </x-admin.card>
        @endforeach
    @endif

    @if ($logs->isNotEmpty())
        <x-admin.card>
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Raw attendance log</h3>
            <ul class="mt-3 divide-y divide-gray-100 text-sm dark:divide-gray-800">
                @foreach ($logs as $log)
                    <li class="flex items-center justify-between py-2">
                        <span class="capitalize font-medium">{{ $log->type?->value ?? $log->type }}</span>
                        <span class="text-gray-500">{{ $log->logged_at?->format('H:i:s') }} · {{ $log->source?->value ?? $log->source ?? 'manual' }}</span>
                    </li>
                @endforeach
            </ul>
        </x-admin.card>
    @endif
</x-layouts.business-admin>
