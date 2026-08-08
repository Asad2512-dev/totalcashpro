@props([
    'label',
    'value',
    'change' => null,
    'tone' => 'success',
    'compact' => false,
])

<article @class([
    'admin-card min-w-0 transition hover:-translate-y-0.5 hover:shadow-lift',
    'p-3' => $compact,
    'p-5' => ! $compact,
])>
        <p @class([
            'truncate font-medium text-gray-500 dark:text-gray-400',
            'text-xs leading-snug' => $compact,
            'text-sm' => ! $compact,
        ])>{{ $label }}</p>
        <div @class([
            'mt-1.5 flex min-w-0 flex-col gap-1',
            'sm:mt-2 sm:flex-row sm:items-end sm:justify-between sm:gap-2' => ! $compact,
        ])>
            <p @class([
                'font-display font-extrabold tracking-tight text-gray-900 dark:text-white',
                'text-base leading-tight sm:text-lg' => $compact,
                'text-2xl sm:text-3xl' => ! $compact,
            ])>{{ $value }}</p>
        @if ($change)
            <span class="min-w-0 max-w-full shrink">
                <x-admin.badge :tone="$tone" class="max-w-full truncate text-xs">{{ $change }}</x-admin.badge>
            </span>
        @endif
    </div>
</article>
