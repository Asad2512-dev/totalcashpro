@php
    $prevWeek = $from->copy()->subWeek()->toDateString();
    $nextWeek = $from->copy()->addWeek()->toDateString();
@endphp

<x-layouts.business-admin title="Attendance" active="attendance">
    <x-admin.toolbar title="Weekly attendance" description="Clock in / clock out times from live attendance logs, with hours and pay.">
        <a href="{{ route('business-admin.attendance', ['week' => $prevWeek]) }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold dark:border-gray-700">← Prev</a>
        <form method="GET" class="inline-flex items-center gap-2">
            <x-admin.input type="date" name="week" :value="$weekStart" onchange="this.form.submit()" />
        </form>
        <a href="{{ route('business-admin.attendance', ['week' => $nextWeek]) }}" class="rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold dark:border-gray-700">Next →</a>
    </x-admin.toolbar>

    <p class="mb-4 text-sm text-gray-500">{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</p>

    @if (empty($report))
        <x-admin.empty-state title="No attendance data" description="Staff attendance appears here after PIN clock-ins." />
    @else
        <x-admin.card :padding="false">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50/90 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80">
                        <tr>
                            <th class="sticky left-0 z-10 bg-gray-50/95 px-4 py-3 dark:bg-gray-800/95">Staff</th>
                            @foreach (array_keys($report[0]['days']) as $date)
                                <th class="min-w-[7.5rem] px-2 py-3 text-center">{{ \Illuminate\Support\Carbon::parse($date)->format('D j') }}</th>
                            @endforeach
                            <th class="px-3 py-3 text-right">Hours</th>
                            <th class="px-3 py-3 text-right">Pay</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($report as $row)
                            <tr class="align-top">
                                <td class="sticky left-0 z-10 bg-white px-4 py-3 font-medium text-gray-900 dark:bg-gray-900 dark:text-white">
                                    {{ $row['user']->name }}
                                </td>
                                @foreach ($row['days'] as $dayData)
                                    <td class="px-2 py-3 text-center">
                                        @if (! empty($dayData['slots']))
                                            <div class="space-y-1.5">
                                                @foreach ($dayData['slots'] as $slot)
                                                    <div class="rounded-lg border border-primary-100 bg-primary-50/60 px-2 py-1.5 text-xs dark:border-primary-900/40 dark:bg-primary-900/20">
                                                        <div class="font-semibold text-primary-800 dark:text-primary-300">
                                                            In {{ $slot['in'] }}
                                                        </div>
                                                        <div class="font-semibold {{ $slot['out'] ? 'text-gray-700 dark:text-gray-200' : 'text-amber-600' }}">
                                                            Out {{ $slot['out'] ?? '—' }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                                <div class="text-[11px] font-medium text-gray-500">
                                                    {{ number_format((float) $dayData['hours'], 2) }}h
                                                </div>
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
            </div>
        </x-admin.card>
    @endif
</x-layouts.business-admin>
