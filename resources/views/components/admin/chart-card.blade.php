@props([
    'title',
    'description' => null,
    'bars' => [],
    'empty' => 'No data yet for this period.',
])

<section class="admin-card p-5">
    <div class="mb-4">
        <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
        @if ($description)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
        @endif
    </div>

    @if (count($bars) === 0)
        <div class="flex h-52 flex-col items-center justify-center rounded-2xl bg-gray-50 px-6 text-center dark:bg-gray-900/50">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Nothing to chart</p>
            <p class="mt-1 text-sm text-gray-500">{{ $empty }}</p>
        </div>
    @else
        <div class="flex h-44 min-h-[11rem] items-end gap-1.5 overflow-x-auto rounded-2xl bg-gray-50 px-2 py-4 sm:h-52 sm:gap-2 sm:px-3 dark:bg-gray-900/50">
            @foreach ($bars as $height)
                <div
                    class="min-w-[1.25rem] flex-1 rounded-t-md bg-gradient-to-t from-primary-700 to-primary-400/90 transition hover:opacity-90 sm:min-w-0"
                    @style(['height' => $height.'%', 'min-height' => '4px'])
                ></div>
            @endforeach
        </div>
    @endif
</section>
