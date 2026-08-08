@props([
    'padding' => true,
    'title' => null,
])

<section {{ $attributes->class('admin-card overflow-hidden transition hover:shadow-[0_8px_30px_-20px_rgb(17_24_39_/_0.12)]') }}>
    @if ($title || isset($header))
        <div class="border-b border-gray-200 px-4 py-4 sm:px-5 dark:border-gray-700">
            @if ($title)
                <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
            @endif
            {{ $header ?? '' }}
        </div>
    @endif
    <div @class(['p-3.5 sm:p-5' => $padding])>
        {{ $slot }}
    </div>
</section>
