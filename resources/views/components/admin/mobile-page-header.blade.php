@props([
    'title',
    'description' => null,
])

<div {{ $attributes->class('mb-4 lg:mb-6') }}>
    <h2 class="font-display text-xl font-bold tracking-tight text-gray-900 dark:text-white lg:text-2xl">{{ $title }}</h2>
    @if ($description)
        <p class="mt-1 max-w-2xl text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $description }}</p>
    @endif
    @if (isset($actions) && $actions->isNotEmpty())
        <div class="mt-3 flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endif
</div>
