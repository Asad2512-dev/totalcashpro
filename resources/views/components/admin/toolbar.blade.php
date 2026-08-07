@props([
    'title' => null,
    'description' => null,
    'section' => false,
])

@php
    $actionSlot = isset($actions) && $actions->isNotEmpty() ? $actions : $slot;
@endphp

@if ($section && $title)
    <div {{ $attributes->merge(['class' => 'mb-6']) }}>
        <h2 class="font-display text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h2>
        @if ($description)
            <p class="mt-1 max-w-2xl text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $description }}</p>
        @endif
        @if ($actionSlot->isNotEmpty())
            <div class="mt-3 flex flex-wrap items-center gap-2">{{ $actionSlot }}</div>
        @endif
    </div>
@elseif ($description || $actionSlot->isNotEmpty())
    <div {{ $attributes->class(['mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between']) }}>
        @if ($description)
            <p class="max-w-2xl text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $description }}</p>
        @else
            <div class="min-w-0 flex-1"></div>
        @endif
        @if ($actionSlot->isNotEmpty())
            <div class="flex shrink-0 flex-wrap items-center gap-2">{{ $actionSlot }}</div>
        @endif
    </div>
@endif
