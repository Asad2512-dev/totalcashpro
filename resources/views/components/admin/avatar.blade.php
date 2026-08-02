@props([
    'name' => 'SA',
    'size' => 'md',
])

@php
    $sizeClass = match ($size) {
        'sm' => 'h-8 w-8 text-[10px]',
        'lg' => 'h-12 w-12 text-sm',
        default => 'h-9 w-9 text-xs',
    };
    $initials = collect(explode(' ', $name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('');
@endphp

<span {{ $attributes->class("inline-flex items-center justify-center rounded-xl bg-primary-600 font-bold text-white {$sizeClass}") }}>
    {{ strtoupper($initials) }}
</span>
