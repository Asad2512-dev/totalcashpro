@props([
    'title' => null,
    'subtitle' => null,
])

<article {{ $attributes->class('admin-mobile-card') }}>
    @if ($title || $subtitle || isset($badge))
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                @if ($title)
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($badge)
                <div class="shrink-0">{{ $badge }}</div>
            @endisset
        </div>
    @endif

    @if ($slot->isNotEmpty())
        <div @class(['mt-4' => $title || $subtitle || isset($badge)])>
            {{ $slot }}
        </div>
    @endif

    @isset($actions)
        <div class="mt-4 flex flex-wrap gap-3 border-t border-gray-100 pt-4 dark:border-gray-700">
            {{ $actions }}
        </div>
    @endisset
</article>
