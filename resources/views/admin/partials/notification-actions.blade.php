<div class="flex flex-wrap items-center justify-end gap-2">
    @if (! $notification->isRead())
        <form method="POST" action="{{ route('super-admin.notifications.read', $notification) }}">
            @csrf
            <button type="submit" class="font-semibold text-primary-700 hover:text-primary-800">Mark read</button>
        </form>
    @endif
    @if ($notification->archived_at === null)
        <form method="POST" action="{{ route('super-admin.notifications.archive', $notification) }}">
            @csrf
            <button type="submit" class="font-semibold text-primary-700 hover:text-primary-800">Archive</button>
        </form>
    @endif
    <form method="POST" action="{{ route('super-admin.notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="font-semibold text-red-600 hover:text-red-700">Delete</button>
    </form>
</div>
