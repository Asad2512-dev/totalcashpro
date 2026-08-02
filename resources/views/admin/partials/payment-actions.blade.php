<form method="POST" action="{{ route('super-admin.payments.status', $payment) }}" class="flex flex-wrap items-end gap-2">
    @csrf
    <x-admin.select name="status" label="Update status">
        @foreach (['paid','pending','failed','refunded','cancelled'] as $status)
            <option value="{{ $status }}" @selected(($payment->status->value ?? $payment->status) === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </x-admin.select>
    <x-admin.button size="sm" type="submit">Save status</x-admin.button>
</form>
