@props([
    'title',
    'description' => null,
    'actionLabel' => null,
    'actionHref' => '#',
])

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h2 class="font-display text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $title }}</h2>
        @if ($description)
            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
        @endif
    </div>
    @if ($actionLabel)
        <a href="{{ $actionHref }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700">
            {{ $actionLabel }}
        </a>
    @endif
</div>
