@props([
    'label',
    'value',
    'change' => null,
    'tone' => 'success',
])

<article class="admin-card min-w-0 p-5 transition hover:-translate-y-0.5 hover:shadow-lift">
    <p class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
    <div class="mt-3 flex min-w-0 flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <p class="font-display text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-3xl">{{ $value }}</p>
        @if ($change)
            <span class="min-w-0 max-w-full shrink">
                <x-admin.badge :tone="$tone" class="max-w-full truncate">{{ $change }}</x-admin.badge>
            </span>
        @endif
    </div>
</article>
