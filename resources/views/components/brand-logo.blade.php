@props([
    'height' => 'h-9',
])

@php
    $heightClass = match ($height) {
        'h-7' => 'h-7',
        'h-8' => 'h-8',
        'h-10' => 'h-10',
        'h-11' => 'h-11',
        default => 'h-9',
    };
@endphp

<img
    src="{{ asset('logo.png') }}"
    alt="{{ brand_name() }}"
    decoding="async"
    {{ $attributes->class([
        'block w-auto shrink-0 object-contain object-left',
        $heightClass,
    ]) }}
>
