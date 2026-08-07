<x-layouts.business-admin title="Staff" active="staff">
    <x-admin.toolbar title="Staff" description="Manage team members, PINs, branches and rates.">
        <x-admin.button size="sm" :href="route('business-admin.staff.create')">Add staff</x-admin.button>
    </x-admin.toolbar>

    <form method="GET" class="mb-4 max-w-full sm:max-w-sm">
        <x-admin.input name="q" label="Search" :value="request('q')" placeholder="Name or email" />
    </form>

    @if (session('generated_pin'))
        <x-admin.alert type="warning" class="mb-4">
            New kiosk PIN: <span class="font-mono font-bold">{{ session('generated_pin') }}</span> — copy now. It will not be shown again.
        </x-admin.alert>
    @endif

    @if ($staff->isEmpty())
        <x-admin.empty-state title="No staff yet" description="Add your first team member to start clock-in and payroll.">
            <x-admin.button :href="route('business-admin.staff.create')">Add staff</x-admin.button>
        </x-admin.empty-state>
    @else
        <div class="admin-mobile-cards">
            @foreach ($staff as $member)
                <article class="admin-mobile-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $member->name }}</h3>
                            <p class="mt-1 truncate text-xs text-gray-500">{{ $member->email }}</p>
                        </div>
                        <x-admin.badge tone="neutral">{{ $member->status }}</x-admin.badge>
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Branch</dt>
                            <dd class="mt-1 text-gray-700 dark:text-gray-200">{{ $member->branch?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Kiosk PIN</dt>
                            <dd class="mt-1 text-gray-700 dark:text-gray-200">{{ $member->hasPinConfigured() ? 'Configured' : 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Rate</dt>
                            <dd class="mt-1 text-gray-700 dark:text-gray-200">£{{ number_format((float) ($member->hourly_rate ?? 0), 2) }}</dd>
                        </div>
                    </dl>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('business-admin.staff.edit', $member) }}" class="text-sm font-semibold text-primary-700">Edit</a>
                        <form method="POST" action="{{ route('business-admin.staff.suspend', $member) }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-semibold text-amber-600">Suspend</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="admin-card hidden overflow-hidden md:block">
            <div class="admin-table-wrap -mx-4 sm:mx-0">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="hidden px-4 py-3 lg:table-cell">Branch</th>
                            <th class="px-4 py-3">Kiosk PIN</th>
                            <th class="hidden px-4 py-3 sm:table-cell">Rate</th>
                            <th class="hidden px-4 py-3 md:table-cell">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($staff as $member)
                            <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                                <td class="px-4 py-3.5">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $member->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $member->email }}</p>
                                    <div class="mt-2 space-y-1 text-xs text-gray-500 lg:hidden">
                                        <p><span class="font-medium text-gray-400">Branch:</span> {{ $member->branch?->name ?? '—' }}</p>
                                        <p class="sm:hidden"><span class="font-medium text-gray-400">Rate:</span> £{{ number_format((float) ($member->hourly_rate ?? 0), 2) }}</p>
                                        <p class="md:hidden"><span class="font-medium text-gray-400">Status:</span> {{ $member->status }}</p>
                                    </div>
                                </td>
                                <td class="hidden px-4 py-3.5 text-gray-700 dark:text-gray-200 lg:table-cell">{{ $member->branch?->name ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $member->hasPinConfigured() ? 'Configured' : 'Not set' }}</td>
                                <td class="hidden px-4 py-3.5 text-gray-700 dark:text-gray-200 sm:table-cell">£{{ number_format((float) ($member->hourly_rate ?? 0), 2) }}</td>
                                <td class="hidden px-4 py-3.5 text-gray-700 dark:text-gray-200 md:table-cell">{{ $member->status }}</td>
                                <td class="px-4 py-3.5">
                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('business-admin.staff.edit', $member) }}" class="font-semibold text-primary-700">Edit</a>
                                        <form method="POST" action="{{ route('business-admin.staff.suspend', $member) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="font-semibold text-amber-600">Suspend</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <x-admin.pagination :paginator="$staff" />
    @endif
</x-layouts.business-admin>
