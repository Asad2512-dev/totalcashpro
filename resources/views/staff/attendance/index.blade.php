@php
    $prev = $from->copy()->subWeek()->toDateString();
    $next = $from->copy()->addWeek()->toDateString();
@endphp

<x-layouts.staff title="Attendance" active="attendance">
    <x-admin.toolbar title="My Attendance" description="Your clock-in and clock-out history for the week.">
        <a href="{{ route('staff.attendance', ['week' => $prev]) }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold dark:border-gray-700">← Prev</a>
        <span class="px-2 text-sm font-semibold">{{ $from->format('d M') }} – {{ $to->format('d M Y') }}</span>
        <a href="{{ route('staff.attendance', ['week' => $next]) }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold dark:border-gray-700">Next →</a>
    </x-admin.toolbar>

    <div class="admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50/90 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80">
                    <tr>
                        <th class="px-4 py-3">Day</th>
                        <th class="px-4 py-3">Slots</th>
                        <th class="px-4 py-3">Hours</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($days as $date => $day)
                        <tr>
                            <td class="px-4 py-3.5 font-medium text-gray-900 dark:text-white">{{ \Illuminate\Support\Carbon::parse($date)->format('D d M') }}</td>
                            <td class="px-4 py-3.5 text-gray-600 dark:text-gray-300">
                                @forelse ($day['slots'] as $slot)
                                    <span class="mr-2 inline-block rounded-lg bg-gray-100 px-2 py-1 text-xs dark:bg-gray-800">{{ $slot['in'] }}–{{ $slot['out'] ?? '…' }}</span>
                                @empty
                                    —
                                @endforelse
                            </td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ number_format((float) $day['hours'], 2) }}h</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 dark:bg-gray-800/60">
                        <td class="px-4 py-3 font-bold" colspan="2">Week total</td>
                        <td class="px-4 py-3 font-bold">{{ number_format(collect($days)->sum('hours'), 2) }}h</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-layouts.staff>
