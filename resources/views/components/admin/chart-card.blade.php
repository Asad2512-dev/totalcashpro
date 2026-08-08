@props([
    'title',
    'description' => null,
    'bars' => [],
    'points' => [],
    'empty' => 'No data yet for this period.',
    'total' => null,
])

@php
    $chartHeight = 144;
    $normalized = [];

    if (count($points) > 0) {
        $normalized = $points;
    } elseif (count($bars) > 0) {
        foreach ($bars as $percent) {
            $normalized[] = [
                'label' => '',
                'value' => '',
                'height' => max(4, (int) round(((int) $percent / 100) * $chartHeight)),
            ];
        }
    }

    $hasData = count($normalized) > 0;
    if (collect($normalized)->contains(fn (array $point): bool => array_key_exists('amount', $point))) {
        $hasData = collect($normalized)->sum(fn (array $point): float => (float) ($point['amount'] ?? 0)) > 0;
    } else {
        $hasData = collect($normalized)->max(fn (array $point): int => (int) ($point['height'] ?? 0)) >= 4;
    }

    $chartPoints = array_values($normalized);
@endphp

<section
    class="admin-card admin-chart-card overflow-visible p-5 pb-6"
    @if ($hasData && count($normalized) > 0)
        x-data="{
            points: @js($chartPoints),
            tip: null,
            tipX: 0,
            tipY: 0,
            placeAbove: true,
            showTip(index, event) {
                const point = this.points[index];
                if (! point) return;
                if (! (point.details?.length) && ! point.value) return;
                this.tip = point;
                const bar = event.currentTarget;
                const rect = bar.getBoundingClientRect();
                const rowCount = point.details?.length ?? 1;
                const estimatedHeight = point.details ? 44 + rowCount * 18 : 28;
                this.tipX = Math.min(Math.max(rect.left + rect.width / 2, 140), window.innerWidth - 140);
                if (rect.top < estimatedHeight + 16) {
                    this.placeAbove = false;
                    this.tipY = rect.bottom + 8;
                } else {
                    this.placeAbove = true;
                    this.tipY = rect.top - 8;
                }
            },
            hideTip() {
                this.tip = null;
            },
        }"
        x-init="
            const hideOnScroll = () => hideTip();
            window.addEventListener('scroll', hideOnScroll, true);
            $el.addEventListener('scroll', hideOnScroll, true);
            $cleanup(() => {
                window.removeEventListener('scroll', hideOnScroll, true);
                $el.removeEventListener('scroll', hideOnScroll, true);
            });
        "
    @endif
>
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
            @if ($description)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
            @endif
        </div>
        @if ($total)
            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $total }}</p>
        @endif
    </div>

    @if (count($normalized) === 0 || ! $hasData)
        <div class="flex h-52 flex-col items-center justify-center rounded-2xl bg-gray-50 px-6 text-center dark:bg-gray-900/50">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Nothing to chart</p>
            <p class="mt-1 text-sm text-gray-500">{{ $empty }}</p>
        </div>
    @else
        <div class="admin-chart-panel rounded-2xl bg-gray-50 px-2 pb-2 pt-2 sm:px-3 sm:pb-4 dark:bg-gray-900/50">
            <div class="admin-chart-scroll overflow-x-auto overscroll-x-contain">
                <div class="min-w-full">
                    <div class="flex items-end gap-1 sm:gap-1.5" style="height: {{ $chartHeight }}px;">
                        @foreach ($normalized as $index => $point)
                            <div class="flex min-w-[2rem] flex-1 flex-col items-center sm:min-w-0">
                                <div class="flex w-full flex-1 items-end">
                                    <div
                                        class="w-full cursor-default rounded-t-md bg-gradient-to-t from-primary-700 to-primary-400 transition hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/50"
                                        style="height: {{ max(0, (int) ($point['height'] ?? 0)) }}px; min-height: {{ ($point['amount'] ?? 1) > 0 ? '4px' : '0' }}"
                                        tabindex="0"
                                        @mouseenter="showTip({{ $index }}, $event)"
                                        @mouseleave="hideTip()"
                                        @focus="showTip({{ $index }}, $event)"
                                        @blur="hideTip()"
                                        @if (! empty($point['date_label'])) aria-label="{{ $point['date_label'] }} · {{ $point['value'] ?? '' }}" @endif
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if (collect($normalized)->contains(fn (array $point): bool => ! empty($point['label'])))
                        <div class="admin-chart-axis mt-3 flex gap-1 pb-1 sm:gap-1.5">
                            @foreach ($normalized as $point)
                                <div class="min-w-[2rem] flex-1 truncate text-center text-[10px] font-medium leading-tight text-gray-500 sm:min-w-0 dark:text-gray-400">
                                    {{ $point['label'] ?? '' }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <template x-teleport="body">
            <div
                x-show="tip"
                x-cloak
                class="pointer-events-none fixed z-[300] min-w-[11rem] max-w-[14rem]"
                :class="placeAbove ? '-translate-x-1/2 -translate-y-full' : '-translate-x-1/2'"
                :style="`left: ${tipX}px; top: ${tipY}px`"
                role="tooltip"
            >
                <div
                    x-show="tip?.details?.length"
                    class="rounded-xl border border-gray-800/10 bg-gray-900 px-3 py-2.5 text-left shadow-lg dark:border-gray-200/10 dark:bg-gray-100"
                >
                    <p
                        x-show="tip?.date_label"
                        x-text="tip?.date_label"
                        class="mb-2 border-b border-white/15 pb-1.5 text-[11px] font-bold text-white dark:border-gray-300 dark:text-gray-900"
                    ></p>
                    <dl class="space-y-1">
                        <template x-for="row in tip?.details ?? []" :key="row.label">
                            <div
                                class="flex items-center justify-between gap-4 text-[10px]"
                                :class="row.emphasis ? 'font-bold text-white dark:text-gray-900' : 'text-white/80 dark:text-gray-600'"
                            >
                                <dt x-text="row.label"></dt>
                                <dd
                                    x-text="row.value"
                                    :class="row.emphasis ? '' : 'font-semibold text-white dark:text-gray-900'"
                                ></dd>
                            </div>
                        </template>
                    </dl>
                </div>
                <div
                    x-show="tip && ! tip?.details?.length && tip?.value"
                    x-text="tip?.value"
                    class="whitespace-nowrap rounded-md bg-gray-900 px-2 py-0.5 text-center text-[10px] font-semibold text-white shadow-sm dark:bg-gray-100 dark:text-gray-900"
                ></div>
            </div>
        </template>
    @endif
</section>
