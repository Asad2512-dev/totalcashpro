@props([
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
    $variantClasses = match ($variant) {
        'neutral' => 'border border-gray-200 bg-white text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700',
        'danger' => 'border border-red-200 bg-red-50 text-red-700 shadow-sm hover:bg-red-100 dark:border-red-900/40 dark:bg-red-950/30 dark:text-red-300 dark:hover:bg-red-950/50',
        'success' => 'border border-emerald-200 bg-emerald-50 text-emerald-800 shadow-sm hover:bg-emerald-100 dark:border-emerald-900/40 dark:bg-emerald-950/30 dark:text-emerald-300 dark:hover:bg-emerald-950/50',
        'warning' => 'border border-amber-200 bg-amber-50 text-amber-800 shadow-sm hover:bg-amber-100 dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-300 dark:hover:bg-amber-950/50',
        default => 'border border-primary-200 bg-primary-50 text-primary-800 shadow-sm hover:bg-primary-100 dark:border-primary-800/50 dark:bg-primary-950/40 dark:text-primary-300 dark:hover:bg-primary-900/50',
    };

    $classes = "admin-table-action inline-flex min-h-[32px] shrink-0 items-center justify-center rounded-lg px-2.5 py-1.5 text-xs font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/40 disabled:pointer-events-none disabled:opacity-60 {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </button>
@endif
