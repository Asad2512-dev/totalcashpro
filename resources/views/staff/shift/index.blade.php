@php
    $prev = \Illuminate\Support\Carbon::parse($weekStart)->subWeek()->toDateString();
    $next = \Illuminate\Support\Carbon::parse($weekStart)->addWeek()->toDateString();
@endphp

<x-layouts.staff title="My Shift" active="shift">
    <x-admin.toolbar title="My Shift" description="Shifts assigned to you this week.">
        <a href="{{ route('staff.shift', ['week' => $prev]) }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold dark:border-gray-700">← Prev</a>
        <span class="px-2 text-sm font-semibold">{{ $weekLabel }}</span>
        <a href="{{ route('staff.shift', ['week' => $next]) }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold dark:border-gray-700">Next →</a>
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
        <div class="admin-card overflow-hidden">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50/90 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Time</th>
                        <th class="px-4 py-3">Section</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($shifts as $shift)
                        <tr>
                            <td class="px-4 py-3.5">{{ $shift->shift_date?->format('D d M') }}</td>
                            <td class="px-4 py-3.5">{{ $shift->shift_type }}</td>
                            <td class="px-4 py-3.5">{{ $shift->start_time?->format('H:i') }}–{{ $shift->end_time?->format('H:i') }}</td>
                            <td class="px-4 py-3.5">
                                @if ($shift->rotaSection)
                                    <span class="inline-flex items-center gap-2">
                                        <span class="inline-block h-3 w-3 rounded" style="background: {{ $shift->rotaSection->color }}"></span>
                                        {{ $shift->rotaSection->name }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.staff>
