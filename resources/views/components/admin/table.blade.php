@props([
    'columns' => [],
    'rows' => [],
    'empty' => 'No records yet.',
    'rawHtml' => false,
])

<div class="admin-card overflow-hidden">
    @if (count($rows) === 0)
        <x-admin.empty-state :title="$empty" description="Records will appear here once data is available." />
    @else
        <div class="admin-table-wrap -mx-4 sm:mx-0">
            <table class="admin-table min-w-full text-left text-sm">
                <thead>
                    <tr>
                        @foreach ($columns as $index => $column)
                            <th @class([
                                'whitespace-nowrap px-4 py-3',
                                'hidden md:table-cell' => $index > 0 && $index >= max(count($columns) - 2, 2),
                                'hidden sm:table-cell' => $index > 0 && $index === 1 && count($columns) > 4,
                            ])>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            @foreach ($row as $index => $cell)
                                <td @class([
                                    'px-4 py-3.5 text-gray-700 dark:text-gray-200',
                                    'whitespace-nowrap' => $index > 0,
                                    'hidden md:table-cell' => $index > 0 && $index >= max(count($columns) - 2, 2),
                                    'hidden sm:table-cell' => $index > 0 && $index === 1 && count($columns) > 4,
                                ])>
                                    @if ($index === 0)
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            @if ($rawHtml)
                                                {!! $cell !!}
                                            @else
                                                {{ $cell }}
                                            @endif
                                        </div>
                                        @if (count($columns) > 1)
                                            <div class="mt-1 space-y-0.5 text-xs text-gray-500 sm:hidden">
                                                @foreach (array_slice($row, 1) as $mobileIndex => $mobileCell)
                                                    @if ($mobileCell !== '' && $mobileCell !== null)
                                                        <p><span class="font-medium text-gray-400">{{ $columns[$mobileIndex + 1] ?? '' }}:</span>
                                                            @if ($rawHtml)
                                                                {!! $mobileCell !!}
                                                            @else
                                                                {{ $mobileCell }}
                                                            @endif
                                                        </p>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        @if ($rawHtml)
                                            {!! $cell !!}
                                        @else
                                            {{ $cell }}
                                        @endif
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
