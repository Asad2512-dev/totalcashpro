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
        <div class="flex h-52 items-end gap-2 rounded-2xl bg-gray-50 px-3 py-4 dark:bg-gray-900/50">
            @foreach ($bars as $height)
                <div
                    class="flex-1 rounded-t-md bg-gradient-to-t from-primary-700 to-primary-400/90 transition hover:opacity-90"
                    @style(['height' => $height.'%'])
                ></div>
            @endforeach
        </div>
    @endif
</section>
