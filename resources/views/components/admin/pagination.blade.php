@props(['paginator' => null])

@if ($paginator)
    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-500">
            Showing
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}</span>
            of
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $paginator->total() }}</span>
        </p>
        <div>
            {{ $paginator->onEachSide(1)->links() }}
        </div>
    </div>
@endif
