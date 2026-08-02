@props(['tone' => 'neutral'])

@php
    $classes = match ($tone) {
        'success' => 'bg-primary-50 text-primary-700 ring-primary-600/15 dark:bg-primary-900/30 dark:text-primary-300',
        'warning' => 'bg-amber-50 text-amber-700 ring-amber-600/15 dark:bg-amber-900/30 dark:text-amber-300',
        'danger' => 'bg-red-50 text-red-700 ring-red-600/15 dark:bg-red-900/30 dark:text-red-300',
        'info' => 'bg-sky-50 text-sky-700 ring-sky-600/15 dark:bg-sky-900/30 dark:text-sky-300',
        default => 'bg-gray-100 text-gray-700 ring-gray-500/10 dark:bg-gray-800 dark:text-gray-300',
    };
@endphp

<span {{ $attributes->class("inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {$classes}") }}>
    {{ $slot }}
</span>
