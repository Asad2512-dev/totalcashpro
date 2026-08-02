@props(['tone' => 'info'])

@php
    $classes = match ($tone) {
        'success' => 'border-primary-200 bg-primary-50 text-primary-800 dark:border-primary-800 dark:bg-primary-900/20 dark:text-primary-200',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-200',
        'danger' => 'border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200',
        default => 'border-sky-200 bg-sky-50 text-sky-800 dark:border-sky-800 dark:bg-sky-900/20 dark:text-sky-200',
    };
@endphp

<div {{ $attributes->class("rounded-2xl border px-4 py-3 text-sm {$classes}") }}>
    {{ $slot }}
</div>
