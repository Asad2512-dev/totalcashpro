@props([
    'route' => null,
    'count' => 0,
])

@if ($route)
    <a
        href="{{ route($route) }}"
        class="admin-touch-target relative inline-flex items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700"
        aria-label="Notifications{{ $count > 0 ? " ({$count} unread)" : '' }}"
    >
        <x-admin.icon name="bell" class="h-4 w-4" />
        @if ($count > 0)
            <span class="absolute -right-1 -top-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-primary-600 px-1 text-[10px] font-bold leading-none text-white">
                {{ $count > 9 ? '9+' : $count }}
            </span>
        @endif
    </a>
@endif
