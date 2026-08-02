@props([
    'label',
    'value',
    'change' => null,
    'tone' => 'success',
])

<article class="admin-card p-5">
    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
    <div class="mt-3 flex items-end justify-between gap-3">
        <p class="font-display text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">{{ $value }}</p>
        @if ($change)
            <span @class([
                'rounded-full px-2.5 py-1 text-xs font-semibold',
                'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300' => $tone === 'success',
                'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300' => $tone === 'info',
                'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' => $tone === 'warning',
                'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300' => $tone === 'danger',
            ])>
                {{ $change }}
            </span>
        @endif
    </div>
</article>
