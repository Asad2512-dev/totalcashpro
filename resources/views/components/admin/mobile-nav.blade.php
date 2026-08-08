@props([
    'items' => [],
    'moreGroups' => [],
    'active' => '',
])

@php
    $isActive = function (array $item) use ($active): bool {
        return in_array($active, $item['active'] ?? [], true);
    };
@endphp

<nav class="admin-mobile-nav" aria-label="Primary navigation">
    @foreach ($items as $item)
        @if ($item['more'] ?? false)
            <button
                type="button"
                class="admin-mobile-nav__item {{ $isActive($item) ? 'admin-mobile-nav__item--active' : '' }}"
                @click="mobileMoreOpen = true"
                aria-label="More navigation"
            >
                <x-admin.icon :name="$item['icon']" class="h-6 w-6" />
                <span>{{ $item['label'] }}</span>
            </button>
        @else
            <a
                href="{{ route($item['route']) }}"
                @class([
                    'admin-mobile-nav__item',
                    'admin-mobile-nav__item--active' => $isActive($item),
                ])
            >
                <x-admin.icon :name="$item['icon']" class="h-6 w-6" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endif
    @endforeach
</nav>

<div
    x-cloak
    x-show="mobileMoreOpen"
    x-transition.opacity
    class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-[2px] lg:hidden"
    role="dialog"
    aria-modal="true"
    aria-label="More navigation"
    @keydown.escape.window="mobileMoreOpen = false"
    @click.self="mobileMoreOpen = false"
>
    <div
        x-show="mobileMoreOpen"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="admin-mobile-more-sheet absolute inset-x-0 bottom-0 flex max-h-[min(85dvh,32rem)] flex-col rounded-t-2xl border border-gray-200 bg-white shadow-soft dark:border-gray-700 dark:bg-gray-900"
        @click.stop
    >
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">More</h3>
            <button type="button" class="admin-touch-target rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" @click="mobileMoreOpen = false" aria-label="Close">
                <x-admin.icon name="x" class="h-4 w-4" />
            </button>
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto p-4">
            @foreach ($moreGroups as $group)
                <div @class(['space-y-1', 'mt-4' => ! $loop->first])>
                    <p class="px-1 text-[11px] font-bold uppercase tracking-[0.14em] text-gray-400">{{ $group['label'] }}</p>
                    @foreach ($group['items'] as $link)
                        <a
                            href="{{ route($link['route']) }}"
                            class="admin-touch-target flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-800"
                            @click="mobileMoreOpen = false"
                        >
                            <x-admin.icon :name="$link['icon']" class="h-4 w-4 shrink-0 text-primary-600" />
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
