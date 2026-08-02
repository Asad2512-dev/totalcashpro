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
<form method="POST" action="{{ route('super-admin.organizations.login-as', $organization) }}" class="inline">
    @csrf
    <x-admin.button size="sm" variant="ghost" type="submit">Login As Business</x-admin.button>
</form>
<form method="POST" action="{{ route('super-admin.organizations.destroy', $organization) }}" class="inline" onsubmit="return confirm('Delete this business?')">
    @csrf
    @method('DELETE')
    <x-admin.button size="sm" variant="danger" type="submit">Delete</x-admin.button>
</form>
