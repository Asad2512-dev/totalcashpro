<x-layouts.business-admin title="HR" active="hr">
    <x-admin.toolbar description="Review staff holiday, sick and leave requests." />

    @if ($leaveRequests->isEmpty())
        <x-admin.empty-state title="No pending requests" description="Staff leave requests will appear here." />
    @else
        <div class="space-y-4">
            @foreach ($leaveRequests as $req)
                <x-admin.card>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $req->user?->name }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ $req->type->label() }} · {{ $req->start_date->format('d M') }} – {{ $req->end_date->format('d M Y') }}</p>
                            @if ($req->reason)
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $req->reason }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('business-admin.hr.leave.review', $req) }}" class="flex flex-wrap gap-2">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <x-admin.button type="submit" size="sm">Approve</x-admin.button>
                        </form>
                        <form method="POST" action="{{ route('business-admin.hr.leave.review', $req) }}" class="flex flex-col gap-2 sm:min-w-[200px]">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <x-admin.input name="admin_notes" label="Decline note" />
                            <x-admin.button type="submit" size="sm" variant="secondary">Decline</x-admin.button>
                        </form>
                    </div>
                </x-admin.card>
            @endforeach
        </div>
    @endif

    <x-admin.toolbar section title="Shift swap requests" description="Approve swaps to automatically reassign rota shifts." class="mt-8" />

    @if ($shiftSwaps->isEmpty())
        <x-admin.empty-state title="No pending shift swaps" description="Staff shift swap requests will appear here." />
    @else
        <div class="space-y-4">
            @foreach ($shiftSwaps as $swap)
                <x-admin.card>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $swap->requester?->name }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $swap->rotaShift?->shift_date?->format('d M Y') }} · {{ $swap->rotaShift?->shift_type }}</p>
                    <form method="POST" action="{{ route('business-admin.hr.shift-swap.review', $swap) }}" class="mt-3 flex flex-wrap gap-2">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <x-admin.button type="submit" size="sm">Approve swap</x-admin.button>
                    </form>
                    <form method="POST" action="{{ route('business-admin.hr.shift-swap.review', $swap) }}" class="mt-2 flex flex-col gap-2 sm:max-w-xs">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <x-admin.button type="submit" size="sm" variant="secondary">Decline swap</x-admin.button>
                    </form>
                </x-admin.card>
            @endforeach
        </div>
    @endif
</x-layouts.business-admin>
