@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
])

@php
    $classes = match ($variant) {
        'secondary' => 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700',
        'ghost' => 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        'soft' => 'bg-primary-50 text-primary-700 hover:bg-primary-100 dark:bg-primary-900/30 dark:text-primary-300',
        default => 'bg-primary-600 text-white hover:bg-primary-700 shadow-sm',
    };
    $sizes = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs rounded-lg',
        'lg' => 'px-5 py-3 text-sm rounded-xl',
        default => 'px-4 py-2.5 text-sm rounded-xl',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class("inline-flex items-center justify-center gap-2 font-semibold transition {$classes} {$sizes}") }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class("inline-flex items-center justify-center gap-2 font-semibold transition {$classes} {$sizes}") }}>
        {{ $slot }}
    </button>
@endif
