<x-layouts.business-admin title="Staff" active="staff">
    <x-admin.toolbar title="Staff" description="Manage team members, PINs, branches and rates.">
        <x-admin.button size="sm" :href="route('business-admin.staff.create')">Add staff</x-admin.button>
    </x-admin.toolbar>

    <form method="GET" class="mb-4 max-w-sm">
        <x-admin.input name="q" label="Search" :value="request('q')" placeholder="Name, email, PIN" />
    </form>

    @if ($staff->isEmpty())
        <x-admin.empty-state title="No staff yet" description="Add your first team member to start clock-in and payroll." />
    @else
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Branch</th>
                        <th class="px-4 py-3">PIN</th>
                        <th class="px-4 py-3">Rate</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($staff as $member)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $member->name }}</p>
                                <p class="text-xs text-gray-500">{{ $member->email }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $member->branch?->name ?? '—' }}</td>
                            <td class="px-4 py-3 font-mono">{{ $member->pin_code ?? '—' }}</td>
                            <td class="px-4 py-3">£{{ number_format((float) ($member->hourly_rate ?? 0), 2) }}</td>
                            <td class="px-4 py-3"><x-admin.badge :tone="$member->status === 'active' ? 'success' : 'warning'">{{ $member->status }}</x-admin.badge></td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('business-admin.staff.edit', $member) }}" class="font-semibold text-primary-600">Edit</a>
                                <form method="POST" action="{{ route('business-admin.staff.suspend', $member) }}" class="inline">@csrf<button class="font-semibold text-amber-600">Suspend</button></form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $staff->links() }}</div>
    @endif
</x-layouts.business-admin>
