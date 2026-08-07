@props([
    'sticky' => true,
])

<div {{ $attributes->class('admin-matrix-wrap min-w-0') }}>
    <div class="overflow-x-auto overscroll-x-contain px-3 pb-3 sm:px-4 sm:pb-4">
        {{ $slot }}
    </div>
</div>
