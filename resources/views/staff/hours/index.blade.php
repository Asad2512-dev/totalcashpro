@php
    $prev = \Illuminate\Support\Carbon::parse($weekStart)->subWeek()->toDateString();
    $next = \Illuminate\Support\Carbon::parse($weekStart)->addWeek()->toDateString();
@endphp

<x-layouts.staff title="Weekly Hours" active="hours">
    <x-admin.toolbar title="Weekly Hours" description="Hours worked this week from clock in/out records.">
        <x-admin.nav-pill :href="route('staff.hours', ['week' => $prev])">← Prev</x-admin.nav-pill>
        <span class="inline-flex min-h-[44px] items-center px-2 text-sm font-semibold">{{ $weekLabel }}</span>
        <x-admin.nav-pill :href="route('staff.hours', ['week' => $next])">Next →</x-admin.nav-pill>
    </x-admin.toolbar>

    <x-admin.card class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total this week</p>
        <p class="mt-2 font-display text-3xl font-bold text-primary-700">{{ number_format($totalHours, 2) }}h</p>
    </x-admin.card>

    <div class="space-y-3">
        @foreach ($days as $date => $day)
            <x-admin.card>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ \Illuminate\Support\Carbon::parse($date)->format('D d M') }}</p>
                        @forelse ($day['slots'] as $slot)
                            <p class="mt-1 text-sm text-gray-500">{{ $slot['in'] }} – {{ $slot['out'] ?: '…' }}</p>
                        @empty
                            <p class="mt-1 text-sm text-gray-500">No clock entries</p>
                        @endforelse
                    </div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($day['hours'], 2) }}h</p>
                </div>
            </x-admin.card>
        @endforeach
    </div>
</x-layouts.staff>
