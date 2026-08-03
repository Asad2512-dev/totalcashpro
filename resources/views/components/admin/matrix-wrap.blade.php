@props([
    'sticky' => true,
])

<div {{ $attributes->class('admin-matrix-wrap -mx-4 sm:mx-0') }}>
    <div class="overflow-x-auto overscroll-x-contain">
        {{ $slot }}
    </div>
</div>
