@props([
    'href' => '#',
    'active' => false,
])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'admin-touch-target inline-flex items-center justify-center rounded-xl px-3 text-sm font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/40',
        'bg-primary-600 text-white shadow-sm' => $active,
        'border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800' => ! $active,
    ]) }}
>{{ $slot }}</a>
