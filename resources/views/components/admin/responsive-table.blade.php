@props([
    'breakpoint' => 'md',
    'empty' => null,
])

@php
    $hideMobile = $breakpoint === 'lg' ? 'lg:hidden' : 'md:hidden';
    $showDesktop = $breakpoint === 'lg' ? 'hidden lg:block' : 'hidden md:block';
    $hasMobile = isset($mobile) && $mobile->isNotEmpty();
@endphp

<div {{ $attributes->class('admin-responsive-table') }}>
    @if ($hasMobile)
        <div class="admin-mobile-cards {{ $hideMobile }}">
            {{ $mobile }}
        </div>
    @endif

    <div @class([$hasMobile ? $showDesktop : null])>
        {{ $slot }}
    </div>
</div>
