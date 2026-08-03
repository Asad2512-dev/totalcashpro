@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'loading' => false,
])

@php
    $classes = match ($variant) {
        'secondary' => 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700',
        'outline' => 'border border-gray-200 bg-transparent text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800',
        'ghost' => 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        'soft' => 'bg-primary-50 text-primary-700 hover:bg-primary-100 dark:bg-primary-900/30 dark:text-primary-300',
        default => 'bg-primary-600 text-white hover:bg-primary-700 shadow-sm',
    };
    $sizes = match ($size) {
        'sm' => 'min-h-[40px] px-3 py-2 text-xs rounded-lg sm:min-h-[36px]',
        'lg' => 'min-h-[48px] px-5 py-3 text-sm rounded-xl',
        default => 'min-h-[44px] px-4 py-2.5 text-sm rounded-xl',
    };
    $shared = "inline-flex items-center justify-center gap-2 font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/40 disabled:pointer-events-none disabled:opacity-60 {$classes} {$sizes}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($shared) }}>
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        @if ($loading) disabled @endif
        {{ $attributes->class($shared) }}
    >
        @if ($loading)
            <span class="inline-flex h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" aria-hidden="true"></span>
        @endif
        {{ $slot }}
    </button>
@endif
