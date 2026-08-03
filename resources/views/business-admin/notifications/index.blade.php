<x-layouts.business-admin title="Notifications" active="notifications">
    <x-admin.toolbar title="Notifications" />

    <div class="mt-6">
        <x-admin.card>
            @if ($notifications->isEmpty())
                <x-admin.empty-state
                    icon="bell"
                    title="No notifications"
                    description="You're all caught up!"
                />
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($notifications as $notification)
                        <div class="flex flex-col gap-3 px-4 py-4 transition hover:bg-gray-50 sm:flex-row sm:items-start dark:hover:bg-gray-800">
                            <div class="flex-shrink-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/20">
                                    <x-admin.icon name="bell" class="h-5 w-5 text-primary-600" />
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification->title }}</p>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $notification->message }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex-shrink-0 sm:ml-auto">
                                @if (!$notification->read_at)
                                    <form method="POST" action="{{ route('business-admin.notifications.mark-read', $notification) }}">
                                        @csrf
                                        <button type="submit" class="admin-touch-target text-sm font-semibold text-primary-600 hover:text-primary-700">
                                            Mark Read
                                        </button>
                                    </form>
                                @else
                                    <x-admin.badge tone="secondary">Read</x-admin.badge>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 px-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </x-admin.card>
    </div>
</x-layouts.business-admin>
