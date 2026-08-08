@props([
    'label',
    'value',
    'change' => null,
    'tone' => 'success',
    'compact' => false,
])

<article @class([
    'admin-card min-w-0',
    'p-3 sm:p-4' => $compact,
    'p-4 sm:p-5' => ! $compact,
])>
    <p @class([
        'font-medium text-gray-500 dark:text-gray-400',
        'text-sm' => $compact,
        'text-sm sm:text-base' => ! $compact,
    ])>{{ $label }}</p>
    <div @class([
        'mt-2 flex min-w-0 items-end justify-between gap-2',
        'sm:mt-3' => ! $compact,
    ])>
        <p @class([
            'font-display font-extrabold tracking-tight text-gray-900 dark:text-white',
            'text-xl sm:text-2xl' => $compact,
            'text-2xl sm:text-3xl' => ! $compact,
        ])>{{ $value }}</p>
        @if ($change)
            <span @class([
                'shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold',
                'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300' => $tone === 'success',
                'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300' => $tone === 'info',
                'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' => $tone === 'warning',
                'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $tone === 'neutral',
                'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300' => $tone === 'danger',
            ])>
                {{ $change }}
            </span>
        @endif
    </div>
</article>
