@props([
    'columns' => [],
    'rows' => [],
    'empty' => 'No records yet.',
    'rawHtml' => false,
])

<div class="admin-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50/90 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80 dark:text-gray-400">
                <tr>
                    @foreach ($columns as $column)
                        <th class="px-4 py-3 whitespace-nowrap">{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($rows as $row)
                    <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                        @foreach ($row as $cell)
                            <td class="px-4 py-3.5 whitespace-nowrap text-gray-700 dark:text-gray-200">
                                @if ($rawHtml)
                                    {!! $cell !!}
                                @else
                                    {{ $cell }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ max(count($columns), 1) }}" class="px-4 py-12 text-center text-gray-500">{{ $empty }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
