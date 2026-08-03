@php
    $prev = $from->copy()->subWeek()->toDateString();
    $next = $from->copy()->addWeek()->toDateString();
    $weekTotal = collect($days)->sum('hours');
@endphp

<x-layouts.staff title="Attendance" active="attendance">
    <x-admin.toolbar title="My Attendance" description="Your clock-in and clock-out history for the week.">
        <x-admin.nav-pill :href="route('staff.attendance', ['week' => $prev])">← Prev</x-admin.nav-pill>
        <span class="inline-flex min-h-[44px] items-center px-2 text-sm font-semibold">{{ $from->format('d M') }} – {{ $to->format('d M Y') }}</span>
        <x-admin.nav-pill :href="route('staff.attendance', ['week' => $next])">Next →</x-admin.nav-pill>
    </x-admin.toolbar>

    <div class="admin-mobile-cards">
        @foreach ($days as $date => $day)
            <article class="admin-mobile-card">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ \Illuminate\Support\Carbon::parse($date)->format('D d M') }}</h3>
                    <span class="text-sm font-semibold text-primary-700">{{ number_format((float) $day['hours'], 2) }}h</span>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse ($day['slots'] as $slot)
                        <span class="inline-flex rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ $slot['in'] }}–{{ $slot['out'] ?? '…' }}</span>
                    @empty
                        <span class="text-sm text-gray-400">No slots</span>
                    @endforelse
                </div>
            </article>
        @endforeach
        <article class="admin-mobile-card border-primary-200 bg-primary-50/40 dark:border-primary-900/40 dark:bg-primary-900/15">
            <div class="flex items-center justify-between">
                <span class="font-bold text-gray-900 dark:text-white">Week total</span>
                <span class="font-bold text-primary-700">{{ number_format($weekTotal, 2) }}h</span>
            </div>
        </article>
    </div>

    <x-admin.card :padding="false" class="hidden sm:block">
        <x-admin.table
            :columns="['Day', 'Slots', 'Hours']"
            :rows="collect($days)->map(fn ($day, $date) => [
                \Illuminate\Support\Carbon::parse($date)->format('D d M'),
                collect($day['slots'])->isEmpty()
                    ? '—'
                    : collect($day['slots'])->map(fn ($slot) => $slot['in'].'–'.($slot['out'] ?? '…'))->join(', '),
                number_format((float) $day['hours'], 2).'h',
            ])->values()->push(['Week total', '', number_format($weekTotal, 2).'h'])->all()"
        />
    </x-admin.card>
</x-layouts.staff>
