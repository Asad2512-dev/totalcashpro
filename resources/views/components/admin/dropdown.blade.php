@props([
    'align' => 'right',
    'width' => 'w-48',
])

@php
    $alignment = $align === 'left' ? 'left-0' : 'right-0';
@endphp

<div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false" @click.outside="open = false">
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        {{ $attributes->class("absolute z-40 mt-2 {$width} {$alignment} overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-lift dark:border-gray-700 dark:bg-gray-900") }}
    >
        {{ $slot }}
    </div>
</div>
