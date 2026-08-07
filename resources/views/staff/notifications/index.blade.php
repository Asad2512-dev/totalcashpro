<x-layouts.staff title="Notifications" active="notifications">
    <x-admin.toolbar description="Updates from your organisation.">
        <form method="POST" action="{{ route('staff.notifications.read') }}">
            @csrf
            <x-admin.button size="sm" type="submit">Mark all read</x-admin.button>
        </form>
    </x-admin.toolbar>

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('staff.notifications') }}" class="admin-touch-target inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ empty($activeCategory) ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800' }}">All</a>
        @foreach ($categories as $category)
            <a href="{{ route('staff.notifications', ['category' => $category->value]) }}" class="admin-touch-target inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $activeCategory === $category->value ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-gray-800' }}">{{ $category->label() }}</a>
        @endforeach
    </div>

    @if ($notifications->isEmpty())
        <x-admin.empty-state title="No notifications" description="You’re all caught up." />
    @else
        <div class="space-y-3">
            @foreach ($notifications as $notification)
                <x-admin.card>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $notification->title ?? 'Notification' }}</p>
                            @if ($notification->category)
                                <span class="mt-1 inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold uppercase dark:bg-gray-800">{{ $notification->category instanceof \BackedEnum ? $notification->category->label() : ucfirst((string) $notification->category) }}</span>
                            @endif
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $notification->body ?? $notification->message ?? '' }}</p>
                        </div>
                        <p class="shrink-0 text-xs text-gray-400">{{ $notification->created_at?->diffForHumans() }}</p>
                    </div>
                </x-admin.card>
            @endforeach
        </div>
        <x-admin.pagination :paginator="$notifications" />
    @endif
</x-layouts.staff>
