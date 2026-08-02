@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->class('admin-card') }}>
    @if ($title || $description)
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
            @if ($title)
                <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
            @endif
            @if ($description)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
            @endif
        </div>
    @endif
    <div class="p-5">
        {{ $slot }}
    </div>
</section>
