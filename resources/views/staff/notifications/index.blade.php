<x-layouts.staff title="Notifications" active="notifications">
    <x-admin.toolbar title="Notifications" description="Updates from your organisation.">
        <form method="POST" action="{{ route('staff.notifications.read') }}">
            @csrf
            <x-admin.button size="sm" type="submit">Mark all read</x-admin.button>
        </form>
    </x-admin.toolbar>

    @if ($notifications->isEmpty())
        <x-admin.empty-state title="No notifications" description="You’re all caught up." />
    @else
        <div class="space-y-3">
            @foreach ($notifications as $notification)
                <x-admin.card>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $notification->title ?? 'Notification' }}</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $notification->body ?? $notification->message ?? '' }}</p>
                        </div>
                        <p class="shrink-0 text-xs text-gray-400">{{ $notification->created_at?->diffForHumans() }}</p>
                    </div>
                </x-admin.card>
            @endforeach
        </div>
        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif
</x-layouts.staff>
