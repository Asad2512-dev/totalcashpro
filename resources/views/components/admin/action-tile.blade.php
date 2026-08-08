@props([
    'href' => null,
    'icon' => null,
    'label' => null,
    'variant' => 'default',
])

@php
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class([
        'admin-action-tile',
        'admin-action-tile--primary' => $variant === 'primary',
    ]) }}
    @if (! $href) type="button" @endif
>
    @if ($icon)
        <x-admin.icon :name="$icon" class="admin-action-tile__icon" />
    @endif
    @if ($label)
        <span class="admin-action-tile__label">{{ $label }}</span>
    @endif
    {{ $slot }}
</{{ $tag }}>
