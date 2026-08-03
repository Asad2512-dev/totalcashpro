@php
    $prev = \Illuminate\Support\Carbon::parse($weekStart)->subWeek()->toDateString();
    $next = \Illuminate\Support\Carbon::parse($weekStart)->addWeek()->toDateString();
@endphp

<x-layouts.staff title="My Shift" active="shift">
    <x-admin.toolbar title="My Shift" description="Shifts assigned to you this week.">
        <x-admin.nav-pill :href="route('staff.shift', ['week' => $prev])">← Prev</x-admin.nav-pill>
        <span class="inline-flex min-h-[44px] items-center px-2 text-sm font-semibold">{{ $weekLabel }}</span>
        <x-admin.nav-pill :href="route('staff.shift', ['week' => $next])">Next →</x-admin.nav-pill>
    </x-admin.toolbar>

    @if ($todayShift)
        <x-admin.card class="mb-6">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Today</p>
            <p class="mt-1 font-display text-xl font-bold text-gray-900 dark:text-white">
                {{ $todayShift->shift_type }}
                · {{ $todayShift->start_time?->format('H:i') }}–{{ $todayShift->end_time?->format('H:i') }}
            </p>
            @if ($todayShift->rotaSection)
                <p class="mt-1 text-sm text-gray-500">Section: {{ $todayShift->rotaSection->name }}</p>
            @endif
        </x-admin.card>
    @endif

    @if ($shifts->isEmpty())
        <x-admin.empty-state title="No shifts this week" description="Ask your manager to assign you on the Staff Rota." />
    @else
        <div class="admin-mobile-cards sm:hidden">
            @foreach ($shifts as $shift)
                <article class="admin-mobile-card">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $shift->shift_date?->format('D d M') }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ $shift->shift_type }}</p>
                        </div>
                        <p class="text-sm font-semibold text-primary-700">{{ $shift->start_time?->format('H:i') }}–{{ $shift->end_time?->format('H:i') }}</p>
                    </div>
                    @if ($shift->rotaSection)
                        <p class="mt-3 inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <span class="inline-block h-3 w-3 rounded" style="background: {{ $shift->rotaSection->color }}"></span>
                            {{ $shift->rotaSection->name }}
                        </p>
                    @endif
                </article>
            @endforeach
        </div>

        <x-admin.card :padding="false" class="hidden sm:block">
            <x-admin.table
                :columns="['Date', 'Type', 'Time', 'Section']"
                :rows="$shifts->map(fn ($shift) => [
                    $shift->shift_date?->format('D d M'),
                    $shift->shift_type,
                    $shift->start_time?->format('H:i').'–'.$shift->end_time?->format('H:i'),
                    $shift->rotaSection?->name ?? '—',
                ])->all()"
            />
        </x-admin.card>
    @endif
</x-layouts.staff>
