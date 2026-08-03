@props([
    'name' => 'modal',
    'title' => null,
    'maxWidth' => 'max-w-lg',
])

<div
    x-data="{ open: false }"
    x-cloak
    x-show="open"
    x-transition.opacity
    x-on:open-modal.window="if ($event.detail === @js($name)) open = true"
    x-on:close-modal.window="open = false"
    x-on:keydown.escape.window="if (open) open = false"
    class="admin-modal-overlay fixed inset-0 z-50 flex items-end justify-center bg-gray-900/45 p-0 sm:items-center sm:p-4"
    @click.self="open = false"
    role="dialog"
    aria-modal="true"
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-2"
        {{ $attributes->class("admin-modal-panel admin-card admin-slide-up flex max-h-[92dvh] w-full flex-col p-0 sm:max-h-[85vh] {$maxWidth}") }}
        @click.stop
    >
        @if ($title)
            <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-4 py-4 sm:px-5 dark:border-gray-700">
                <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
                <button type="button" class="admin-touch-target rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" @click="open = false" aria-label="Close">
                    <x-admin.icon name="x" class="h-4 w-4" />
                </button>
            </div>
        @endif
        <div class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-5">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="flex shrink-0 flex-col-reverse gap-2 border-t border-gray-200 px-4 py-4 sm:flex-row sm:justify-end sm:px-5 dark:border-gray-700">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
