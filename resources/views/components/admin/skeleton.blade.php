@props([
    'lines' => 3,
])

@php
    $widths = ['w-full', 'w-5/6', 'w-4/5', 'w-3/4', 'w-2/3'];
@endphp

<div {{ $attributes->class('space-y-3') }}>
    @for ($i = 0; $i < $lines; $i++)
        <div @class(['admin-skeleton h-4', $widths[$i] ?? 'w-1/2'])></div>
    @endfor
</div>
