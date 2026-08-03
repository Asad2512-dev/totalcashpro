@props([
    'name' => 'drawer',
    'title' => null,
    'side' => 'right',
])

@php
    $panelPosition = $side === 'left' ? 'left-0' : 'right-0';
    $enterFrom = $side === 'left' ? '-translate-x-full' : 'translate-x-full';
@endphp

<div
    x-cloak
    x-show="{{ $name }}"
    x-transition.opacity
    class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-[2px]"
    role="dialog"
    aria-modal="true"
    @keydown.escape.window="{{ $name }} = false"
    @click.self="{{ $name }} = false"
>
    <aside
        x-show="{{ $name }}"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="{{ $enterFrom }}"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="{{ $enterFrom }}"
        {{ $attributes->class("admin-drawer-panel absolute inset-y-0 {$panelPosition} flex w-full max-w-[min(100vw,28rem)] flex-col border-gray-200 bg-white shadow-soft dark:border-gray-700 dark:bg-gray-900 {$side === 'left' ? 'border-r' : 'border-l'}") }}
        @click.stop
    >
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-4 py-4 sm:px-5 dark:border-gray-700">
            <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
            <button type="button" class="admin-touch-target rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" @click="{{ $name }} = false" aria-label="Close">
                <x-admin.icon name="x" class="h-4 w-4" />
            </button>
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-5">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="shrink-0 border-t border-gray-200 p-4 sm:p-5 dark:border-gray-700">
                {{ $footer }}
            </div>
        @endisset
    </aside>
</div>
