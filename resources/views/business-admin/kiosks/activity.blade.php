<x-layouts.business-admin :title="$kiosk->name.' Activity'" active="kiosks">
    <x-admin.toolbar :description="'Audit log for '.$kiosk->branch->name">
        <x-slot:actions>
            <x-admin.button href="{{ route('business-admin.kiosks.index') }}" variant="secondary">Back to Kiosks</x-admin.button>
        </x-slot:actions>
    </x-admin.toolbar>

    <x-admin.card>
        <div class="admin-table-wrap">
            <table class="admin-table w-full min-w-[640px]">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Event</th>
                        <th>Staff</th>
                        <th>Device</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap text-sm">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td class="text-sm font-medium">{{ str_replace('_', ' ', ucfirst($log->event)) }}</td>
                            <td class="text-sm">{{ $log->staff?->name ?? '—' }}</td>
                            <td class="text-sm text-gray-500">{{ $log->device_summary ?? '—' }}</td>
                            <td class="font-mono text-xs text-gray-500">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-sm text-gray-500">No activity recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
            <div class="mt-4">{{ $logs->links() }}</div>
        @endif
    </x-admin.card>
</x-layouts.business-admin>
