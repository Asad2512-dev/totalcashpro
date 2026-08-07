<x-admin.table-actions class="justify-end">
    @if (! $notification->isRead())
        <form method="POST" action="{{ route('super-admin.notifications.read', $notification) }}">
            @csrf
            <x-admin.table-action type="submit" variant="success">Mark read</x-admin.table-action>
        </form>
    @endif
    @if ($notification->archived_at === null)
        <form method="POST" action="{{ route('super-admin.notifications.archive', $notification) }}">
            @csrf
            <x-admin.table-action type="submit" variant="neutral">Archive</x-admin.table-action>
        </form>
    @endif
    <form method="POST" action="{{ route('super-admin.notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')">
        @csrf
        @method('DELETE')
        <x-admin.table-action type="submit" variant="danger">Delete</x-admin.table-action>
    </form>
</x-admin.table-actions>
