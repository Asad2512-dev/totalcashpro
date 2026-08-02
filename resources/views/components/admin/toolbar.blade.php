@props([
    'title',
    'description' => null,
])

<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div>
        <h2 class="font-display text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $title }}</h2>
        @if ($description)
            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
        @endif
    </div>
    <div class="flex flex-wrap items-center gap-2">
        {{ $slot }}
    </div>
</div>
