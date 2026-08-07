@php
    $statusTone = static function (string $status): string {
        return match (strtolower($status)) {
            'active', 'open', 'paid', 'admin' => 'success',
            'trial', 'trialing', 'invited', 'staff' => 'info',
            'pending', 'paused', 'past due' => 'warning',
            'failed', 'closed', 'suspended', 'cancelled', 'expired' => 'danger',
            default => 'neutral',
        };
    };
@endphp

<x-layouts.admin :title="$title" active="businesses">
    <x-admin.breadcrumb :items="['Businesses', $title]" />

    <x-admin.toolbar :title="$title" :description="$description">
        <x-admin.button variant="secondary" size="sm" :href="route('super-admin.businesses')">Back</x-admin.button>
        <x-admin.button size="sm" :href="route('super-admin.organizations.edit', $organization)">Edit</x-admin.button>
        {!! $actions !!}
    </x-admin.toolbar>

    @if (! empty($credentials))
        <div class="mb-5 rounded-2xl border border-primary-200 bg-primary-50 px-5 py-4 text-sm text-primary-900 dark:border-primary-800 dark:bg-primary-900/20 dark:text-primary-100">
            <p class="font-semibold">Owner login credentials (share once, then ask them to change password)</p>
            <dl class="mt-3 grid gap-2 sm:grid-cols-3">
                <div>
                    <dt class="text-xs uppercase tracking-wide text-primary-700/80">Owner</dt>
                    <dd class="font-medium">{{ $credentials['name'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-primary-700/80">Email / login</dt>
                    <dd class="font-medium">{{ $credentials['email'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wide text-primary-700/80">Temporary password</dt>
                    <dd class="font-mono font-semibold">{{ $credentials['password'] }}</dd>
                </div>
            </dl>
            <p class="mt-3 text-xs text-primary-800/80 dark:text-primary-200/80">An email was also sent to the owner. With local mail (log driver), copy these details manually if needed.</p>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-3">
        <x-admin.stat label="Branches" :value="(string) $organization->branches->count()" change="Locations" tone="info" />
        <x-admin.stat label="Staff" :value="(string) $organization->users->count()" change="Users on this business" tone="success" />
        <x-admin.stat
            label="Plan"
            :value="$organization->currentSubscription?->plan?->name ?? '—'"
            :change="$organization->currentSubscription?->status?->label() ?? 'No subscription'"
            tone="success"
        />
    </div>

    <div class="mt-6">
        <x-admin.card>
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">Business details</h3>
            <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($fields as $field)
                    <div @class(['sm:col-span-2 lg:col-span-3' => $field['full'] ?? false])>
                        <dt class="text-xs font-semibold uppercase tracking-[0.12em] text-gray-400">{{ $field['label'] }}</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $field['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-admin.card>
    </div>

    <div class="mt-6">
        <x-admin.toolbar
            title="Staff"
            description="All users linked to this business."
        >
            <x-admin.button size="sm" variant="secondary" :href="route('super-admin.users.create')">
                <x-admin.icon name="plus" class="h-4 w-4" />
                Invite user
            </x-admin.button>
        </x-admin.toolbar>

        @if ($organization->users->isEmpty())
            <x-admin.empty-state
                title="No staff yet"
                description="Owner and staff accounts for this business will appear here."
            />
        @else
            <div class="admin-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50/90 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Phone</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Branch</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Last login</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($organization->users as $user)
                                <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <x-admin.avatar :name="$user->name" />
                                            <div>
                                                <p class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                                                @if ($organization->owner_user_id === $user->id)
                                                    <p class="text-xs text-primary-700">Owner</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $user->email }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $user->phone ?? '—' }}</td>
                                    <td class="px-4 py-3.5">
                                        <x-admin.badge :tone="$statusTone($user->role?->name ?? 'neutral')">
                                            {{ $user->role?->name ?? '—' }}
                                        </x-admin.badge>
                                    </td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $user->branch?->name ?? '—' }}</td>
                                    <td class="px-4 py-3.5">
                                        <x-admin.badge :tone="$statusTone($user->status)">{{ ucfirst($user->status) }}</x-admin.badge>
                                    </td>
                                    <td class="px-4 py-3.5 text-gray-500">{{ $user->last_login_at?->format('d M Y H:i') ?? 'Never' }}</td>
                                    <td class="px-4 py-3.5 text-right">
                                        <x-admin.table-action :href="route('super-admin.users.show', $user)" variant="neutral">Manage</x-admin.table-action>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="mt-6">
        <x-admin.toolbar
            title="Branches"
            description="All locations belonging to this business."
        >
            <x-admin.button size="sm" :href="route('super-admin.branches.create', ['organization_id' => $organization->id])">
                <x-admin.icon name="plus" class="h-4 w-4" />
                Add branch
            </x-admin.button>
        </x-admin.toolbar>

        @if ($organization->branches->isEmpty())
            <x-admin.empty-state
                title="No branches yet"
                description="Add the first branch/location for this business."
            />
        @else
            <div class="admin-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50/90 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Branch</th>
                                <th class="px-4 py-3">City</th>
                                <th class="px-4 py-3">Address</th>
                                <th class="px-4 py-3">Staff count</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($organization->branches as $branch)
                                <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                                    <td class="px-4 py-3.5 font-semibold text-gray-900 dark:text-white">{{ $branch->name }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $branch->city ?? '—' }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $branch->address ?? '—' }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $branch->staff_count }}</td>
                                    <td class="px-4 py-3.5">
                                        <x-admin.badge :tone="$statusTone($branch->status)">
                                            {{ ucfirst($branch->status) }}
                                        </x-admin.badge>
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <x-admin.table-actions class="justify-end">
                                            <x-admin.table-action :href="route('super-admin.branches.edit', $branch)" variant="neutral">Edit</x-admin.table-action>
                                            <form method="POST" action="{{ route('super-admin.branches.destroy', $branch) }}" onsubmit="return confirm('Delete this branch?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-admin.table-action type="submit" variant="danger">Delete</x-admin.table-action>
                                            </form>
                                        </x-admin.table-actions>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
