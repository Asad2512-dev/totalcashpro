@if (($accessRequest->status->value ?? $accessRequest->status) === 'pending' || ($accessRequest->status->value ?? $accessRequest->status) === 'reviewed')
    <form method="POST" action="{{ route('super-admin.business-requests.approve', $accessRequest) }}" class="inline">
        @csrf
        <x-admin.button size="sm" type="submit">Approve & Convert</x-admin.button>
    </form>
    <form method="POST" action="{{ route('super-admin.business-requests.reject', $accessRequest) }}" class="inline">
        @csrf
        <x-admin.button size="sm" variant="danger" type="submit">Reject</x-admin.button>
    </form>
@endif
<form method="POST" action="{{ route('super-admin.business-requests.email', $accessRequest) }}" class="inline">
    @csrf
    <x-admin.button size="sm" variant="secondary" type="submit">Send Email</x-admin.button>
</form>
