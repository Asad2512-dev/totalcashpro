@props([
    'columns' => [],
    'rows' => [],
    'empty' => 'No records yet — UI placeholder for Phase 2.',
])

<div class="admin-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80 dark:text-gray-400">
                <tr>
                    @foreach ($columns as $column)
                        <th class="px-4 py-3">{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($rows as $row)
                    <tr class="transition hover:bg-gray-50/80 dark:hover:bg-gray-800/60">
                        @foreach ($row as $cell)
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ max(count($columns), 1) }}" class="px-4 py-10 text-center text-gray-500">
                            {{ $empty }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
