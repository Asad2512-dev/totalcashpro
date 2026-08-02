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
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/45 p-4"
    @click.self="open = false"
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        {{ $attributes->class("admin-card admin-slide-up w-full {$maxWidth} p-0") }}
        @click.stop
    >
        @if ($title)
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
                <button type="button" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" @click="open = false">
                    <x-admin.icon name="x" class="h-4 w-4" />
                </button>
            </div>
        @endif
        <div class="p-5">
            {{ $slot }}
        </div>
        @isset($footer)
            <div class="flex justify-end gap-2 border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
