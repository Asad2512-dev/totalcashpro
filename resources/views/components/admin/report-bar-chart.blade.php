@props([
    'title',
    'description' => null,
    'items' => [],
    'labelKey' => 'label',
    'valueKey' => 'value',
    'empty' => 'No data for this period.',
])

@php
    $rows = collect($items);
    $max = max($rows->pluck($valueKey)->max() ?: 0, 1);
    $hasData = $rows->contains(fn ($item) => (float) ($item[$valueKey] ?? 0) > 0);
@endphp

<section class="admin-card p-5">
    <div class="mb-4">
        <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
        @if ($description)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
        @endif
    </div>

    @if (! $hasData)
        <x-admin.empty-state title="Nothing to chart" :description="$empty" />
    @else
        <div class="space-y-3">
            @foreach ($rows as $item)
                @php
                    $value = (float) ($item[$valueKey] ?? 0);
                    $width = max(4, (int) round(($value / $max) * 100));
                @endphp
                <div>
                    <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                        <span class="truncate font-medium text-gray-700 dark:text-gray-200">{{ $item[$labelKey] ?? '—' }}</span>
                        <span class="shrink-0 font-semibold text-gray-900 dark:text-white">
                            @if (is_numeric($item[$valueKey] ?? null))
                                £{{ number_format($value, 2) }}
                            @else
                                {{ $item[$valueKey] }}
                            @endif
                        </span>
                    </div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-full rounded-full bg-gradient-to-r from-primary-600 to-primary-400 transition-all" style="width: {{ $width }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
