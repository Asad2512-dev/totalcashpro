@props([
    'title',
    'description' => null,
    'series' => [],
    'empty' => 'No data for this period.',
    'format' => 'currency',
])

@php
    $points = collect($series)->values();
    $max = max($points->pluck('value')->max() ?: 0, 1);
    $hasData = $points->contains(fn ($p) => (float) ($p['value'] ?? 0) > 0);
@endphp

<section
    class="admin-card p-5"
    x-data="reportLineChart(@js($points->all()), @js($format))"
>
    <div class="mb-4 flex items-start justify-between gap-3">
        <div>
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
            @if ($description)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
            @endif
        </div>
        <p class="text-sm font-semibold text-primary-700" x-text="hoverLabel" x-show="hoverLabel"></p>
    </div>

    @if (! $hasData)
        <x-admin.empty-state :title="'Nothing to chart'" :description="$empty" />
    @else
        <div class="relative h-52 w-full">
            <svg viewBox="0 0 100 40" preserveAspectRatio="none" class="h-full w-full overflow-visible">
                <defs>
                    <linearGradient id="reportLineFill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="rgb(34 197 94 / 0.35)" />
                        <stop offset="100%" stop-color="rgb(34 197 94 / 0.02)" />
                    </linearGradient>
                </defs>
                <path :d="areaPath" fill="url(#reportLineFill)" />
                <path :d="linePath" fill="none" stroke="rgb(22 163 74)" stroke-width="0.6" vector-effect="non-scaling-stroke" />
                <template x-for="(point, index) in points" :key="index">
                    <circle
                        :cx="point.x"
                        :cy="point.y"
                        r="1.2"
                        class="fill-primary-600 stroke-white stroke-[0.15]"
                        @mouseenter="setHover(index)"
                        @mouseleave="clearHover()"
                    />
                </template>
            </svg>
            <div class="mt-2 flex justify-between gap-1 overflow-x-auto text-[10px] font-medium text-gray-400 sm:text-xs">
                @foreach ($points->take(7) as $point)
                    <span class="whitespace-nowrap">{{ $point['label'] ?? '' }}</span>
                @endforeach
            </div>
        </div>
    @endif
</section>
