<x-layouts.business-admin :title="$kiosk->name.' Activity'" active="kiosks">
    <x-admin.toolbar :title="$kiosk->name" :description="'Audit log for '.$kiosk->branch->name">
        <x-admin.button href="{{ route('business-admin.kiosks.index') }}" variant="secondary" size="sm">Back to Kiosks</x-admin.button>
    </x-admin.toolbar>

    <x-admin.table-shell sticky>
        <thead>
            <tr>
                <th class="px-4 py-3">Time</th>
                <th class="px-4 py-3">Event</th>
                <th class="hidden px-4 py-3 sm:table-cell">Staff</th>
                <th class="hidden px-4 py-3 md:table-cell">Device</th>
                <th class="hidden px-4 py-3 lg:table-cell">IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td class="admin-table-stack-title whitespace-nowrap px-4 py-3.5 text-sm" data-label="Time">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                    <td class="px-4 py-3.5 text-sm font-medium" data-label="Event">{{ str_replace('_', ' ', ucfirst($log->event)) }}</td>
                    <td class="hidden px-4 py-3.5 text-sm sm:table-cell" data-label="Staff">{{ $log->staff?->name ?? '—' }}</td>
                    <td class="hidden px-4 py-3.5 text-sm text-gray-500 md:table-cell" data-label="Device">{{ $log->device_summary ?? '—' }}</td>
                    <td class="hidden px-4 py-3.5 font-mono text-xs text-gray-500 lg:table-cell" data-label="IP">{{ $log->ip_address ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No activity recorded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </x-admin.table-shell>

    @if ($logs->hasPages())
        <div class="admin-pagination mt-4">{{ $logs->links() }}</div>
    @endif
</x-layouts.business-admin>
