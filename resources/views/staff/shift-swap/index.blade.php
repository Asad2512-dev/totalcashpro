<x-layouts.staff title="Shift Swaps" active="shift-swap">
    <x-admin.toolbar title="Shift Swap Requests" description="Ask to swap one of your upcoming shifts." />

    <x-admin.card class="mb-6">
        <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Request swap</h3>
        <form method="POST" action="{{ route('staff.shift-swap.store') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Your shift</label>
                <select name="rota_shift_id" class="admin-input w-full" required>
                    <option value="">Select shift…</option>
                    @foreach ($shifts as $shift)
                        <option value="{{ $shift->id }}">
                            {{ $shift->shift_date?->format('D d M') }} · {{ $shift->shift_type }} · {{ $shift->start_time?->format('H:i') }}–{{ $shift->end_time?->format('H:i') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <x-admin.textarea name="reason" label="Reason (optional)" rows="3" />
            <div class="flex justify-end">
                <x-admin.button type="submit">Submit swap request</x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card>
        <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Your swap requests</h3>
        @if ($requests->isEmpty())
            <p class="mt-4 text-sm text-gray-500">No shift swap requests yet.</p>
        @else
            <div class="mt-4 space-y-3">
                @foreach ($requests as $req)
                    <div class="rounded-xl border border-gray-100 px-3 py-2.5 dark:border-gray-700">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $req->rotaShift?->shift_date?->format('D d M') }} · {{ $req->rotaShift?->shift_type }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">{{ ucfirst($req->status->value) }} · {{ $req->created_at?->diffForHumans() }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </x-admin.card>
</x-layouts.staff>
