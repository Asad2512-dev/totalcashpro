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
    class="admin-card admin-report-line-chart-card p-5 pb-6"
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
        <div class="admin-report-line-chart">
            <div class="admin-report-line-chart__plot relative h-40 w-full sm:h-48">
                <svg viewBox="0 0 100 40" preserveAspectRatio="none" class="h-full w-full overflow-visible">
                    <defs>
                        <linearGradient id="reportLineFill-{{ md5($title) }}" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="rgb(34 197 94 / 0.35)" />
                            <stop offset="100%" stop-color="rgb(34 197 94 / 0.02)" />
                        </linearGradient>
                    </defs>
                    <path :d="areaPath" fill="url(#reportLineFill-{{ md5($title) }})" />
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
            </div>
            <div class="admin-report-line-chart__axis mt-3 flex gap-1 overflow-x-auto overscroll-x-contain pb-1 text-xs font-medium text-gray-400 dark:text-gray-500">
                @foreach ($points as $point)
                    <span class="min-w-[2.75rem] flex-1 whitespace-nowrap text-center">{{ $point['label'] ?? '' }}</span>
                @endforeach
            </div>
        </div>
    @endif
</section>
