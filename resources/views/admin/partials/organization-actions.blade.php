@if ($organization->owner)
    <form method="POST" action="{{ route('super-admin.organizations.send-credentials', $organization) }}" class="inline" onsubmit="return confirm('Reset the owner password and email new login credentials?')">
        @csrf
        <x-admin.button size="sm" variant="soft" type="submit">Send credentials</x-admin.button>
    </form>
@endif
<form method="POST" action="{{ route('super-admin.organizations.activate', $organization) }}" class="inline">
    @csrf
    <x-admin.button size="sm" variant="soft" type="submit">Activate</x-admin.button>
</form>
<form method="POST" action="{{ route('super-admin.organizations.suspend', $organization) }}" class="inline">
    @csrf
    <x-admin.button size="sm" variant="secondary" type="submit">Suspend</x-admin.button>
</form>
<form method="POST" action="{{ route('super-admin.organizations.login-as', $organization) }}" class="inline-flex flex-wrap items-center gap-2">
    @csrf
    <input
        type="text"
        name="reason"
        required
        minlength="3"
        maxlength="500"
        placeholder="Reason for login"
        class="min-h-[36px] rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs dark:border-gray-700 dark:bg-gray-900"
    >
    <x-admin.button size="sm" variant="ghost" type="submit">Login As Business</x-admin.button>
</form>
<form method="POST" action="{{ route('super-admin.organizations.destroy', $organization) }}" class="inline" onsubmit="return confirm('Delete this business?')">
    @csrf
    @method('DELETE')
    <x-admin.button size="sm" variant="danger" type="submit">Delete</x-admin.button>
</form>
