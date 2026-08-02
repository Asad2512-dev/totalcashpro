@php
    $statusTone = static function (string $status): string {
        return match (strtolower($status)) {
            'active', 'open', 'paid', 'published', 'healthy' => 'success',
            'trial', 'scheduled', 'invited', 'info' => 'info',
            'past due', 'high', 'warning', 'ending soon' => 'warning',
            'failed', 'closed', 'danger', 'suspended' => 'danger',
            default => 'neutral',
        };
    };
@endphp

<x-layouts.admin :title="$title" :active="$active">
    <x-admin.breadcrumb :items="[$title]" />

    <x-admin.toolbar :title="$title" :description="$description">
        @if ($actionLabel && ! empty($createRoute))
            <x-admin.button size="sm" :href="route($createRoute)">
                <x-admin.icon name="plus" class="h-4 w-4" />
                {{ $actionLabel }}
            </x-admin.button>
        @elseif ($actionLabel)
            <x-admin.button size="sm">
                <x-admin.icon name="plus" class="h-4 w-4" />
                {{ $actionLabel }}
            </x-admin.button>
        @endif
        <x-admin.button variant="secondary" size="sm" :href="route('super-admin.export', ['type' => $active === 'users' ? 'users' : ($active === 'subscriptions' ? 'subscriptions' : ($active === 'payments' ? 'payments' : ($active === 'support' ? 'support' : 'businesses')))])">Export</x-admin.button>
    </x-admin.toolbar>

    @if ($showFilters ?? true)
        <x-admin.filter-bar />
    @endif

    @if (($layout ?? 'table') === 'plans')
        @if (count($planCards ?? []) === 0)
            <x-admin.empty-state title="No plans configured" description="Seed or create subscription plans to manage pricing." />
        @else
            <div class="grid gap-5 lg:grid-cols-3">
                @foreach ($planCards ?? [] as $plan)
                    <article @class([
                        'admin-card relative p-6 transition hover:-translate-y-1',
                        'ring-2 ring-primary-600/30' => $plan['featured'] ?? false,
                    ])>
                        @if ($plan['featured'] ?? false)
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-primary-700 px-3 py-1 text-[11px] font-bold uppercase tracking-wide text-white">Most popular</span>
                        @endif
                        <p class="text-sm font-semibold text-primary-700">{{ $plan['badge'] }}</p>
                        <h3 class="mt-2 font-display text-2xl font-bold text-gray-900 dark:text-white">{{ $plan['name'] }}</h3>
                        <p class="mt-2 text-sm text-gray-500">{{ $plan['description'] }}</p>
                        <p class="mt-6 font-display text-4xl font-extrabold text-gray-900 dark:text-white">{{ $plan['price'] }}<span class="text-base font-semibold text-gray-500">{{ $plan['period'] }}</span></p>
                        <ul class="mt-6 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                            @foreach ($plan['features'] as $feature)
                                <li class="flex gap-2"><x-admin.icon name="check" class="mt-0.5 h-4 w-4 text-primary-600" /> {{ $feature }}</li>
                            @endforeach
                        </ul>
                    @if (! empty($plan['id']))
                        <div class="mt-8 grid gap-2">
                            <x-admin.button class="w-full" variant="{{ ($plan['featured'] ?? false) ? 'primary' : 'secondary' }}" :href="route('super-admin.plans.edit', $plan['id'])">Manage plan</x-admin.button>
                            <form method="POST" action="{{ route(($plan['is_active'] ?? true) ? 'super-admin.plans.disable' : 'super-admin.plans.enable', $plan['id']) }}">
                                @csrf
                                <x-admin.button class="w-full" variant="ghost" type="submit">{{ ($plan['is_active'] ?? true) ? 'Disable' : 'Enable' }}</x-admin.button>
                            </form>
                        </div>
                    @else
                        <x-admin.button class="mt-8 w-full" variant="{{ ($plan['featured'] ?? false) ? 'primary' : 'secondary' }}">Manage plan</x-admin.button>
                    @endif
                </article>
            @endforeach
        </div>
        @endif

    @elseif (($layout ?? 'table') === 'settings')
        <div class="grid gap-4 lg:grid-cols-[16rem_1fr]" x-data="adminSettings">
            <aside class="admin-card h-fit p-3">
                <nav class="space-y-1 text-sm">
                    @foreach (array_keys($settingsTabs ?? []) as $tab)
                        <button
                            type="button"
                            @click="tab = @js($tab)"
                            :class="tab === @js($tab) ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800'"
                            class="block w-full rounded-xl px-3 py-2.5 text-left font-medium transition"
                        >{{ $tab }}</button>
                    @endforeach
                </nav>
            </aside>

            <div>
                @foreach (($settingsTabs ?? []) as $tabName => $fields)
                    <div x-show="tab === @js($tabName)" x-cloak class="admin-slide-up">
                        <x-admin.card>
                            <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">{{ $tabName }} settings</h3>
                            <p class="mt-1 text-sm text-gray-500">Changes are saved to the database immediately.</p>
                            <form method="POST" action="{{ route('super-admin.settings.update') }}" class="mt-6 space-y-5">
                                @csrf
                                @method('PUT')
                                <div class="grid gap-4 sm:grid-cols-2">
                                    @foreach ($fields as $field)
                                        <x-admin.input
                                            :label="$field['label']"
                                            :name="'settings['.$field['group'].']['.$field['key'].']'"
                                            :value="$field['value']"
                                        />
                                    @endforeach
                                </div>
                                <x-admin.button type="submit">Save changes</x-admin.button>
                            </form>
                        </x-admin.card>
                    </div>
                @endforeach
            </div>
        </div>

    @elseif (($layout ?? 'table') === 'businesses')
        @if (count($records ?? []) === 0)
            <x-admin.empty-state title="No businesses yet" description="Organisations created after access approval will appear in this table." />
        @else
            @php $ids = collect($records)->pluck('id')->all(); @endphp
            <div x-data="adminBulkTable" class="space-y-3">
                <div
                    x-cloak
                    x-show="selected.length"
                    class="flex flex-wrap items-center gap-2 rounded-2xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm text-primary-800 dark:border-primary-800 dark:bg-primary-900/20 dark:text-primary-200"
                >
                    <span x-text="selected.length + ' selected'"></span>
                    <form method="POST" action="{{ route('super-admin.organizations.bulk') }}" class="flex flex-wrap items-center gap-2" @submit="selected.forEach((id) => { const input = document.createElement('input'); input.type='hidden'; input.name='ids[]'; input.value=id; $el.appendChild(input); })">
                        @csrf
                        <input type="hidden" name="bulk_action" value="suspend">
                        <x-admin.button size="sm" variant="secondary" type="submit">Suspend</x-admin.button>
                    </form>
                    <form method="POST" action="{{ route('super-admin.organizations.bulk') }}" class="flex flex-wrap items-center gap-2" onsubmit="return confirm('Delete selected businesses?')" @submit="selected.forEach((id) => { const input = document.createElement('input'); input.type='hidden'; input.name='ids[]'; input.value=id; $el.appendChild(input); })">
                        @csrf
                        <input type="hidden" name="bulk_action" value="delete">
                        <x-admin.button size="sm" variant="danger" type="submit">Delete</x-admin.button>
                    </form>
                </div>

                <div class="admin-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm">
                            <thead class="border-b border-gray-200 bg-gray-50/90 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">
                                        <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600" @change="toggleAll(@js($ids))" :checked="selected.length === @js(count($ids))">
                                    </th>
                                    <th class="px-4 py-3">Business</th>
                                    <th class="px-4 py-3">Plan</th>
                                    <th class="px-4 py-3">Branches</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Owner</th>
                                    <th class="px-4 py-3">Created</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($records as $record)
                                    <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                                        <td class="px-4 py-3.5">
                                            <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-primary-600" :checked="selected.includes({{ $record['id'] }})" @change="toggle({{ $record['id'] }})">
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <a href="{{ route('super-admin.organizations.show', $record['id']) }}" class="font-semibold text-primary-700 hover:text-primary-800 hover:underline">
                                                {{ $record['name'] }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $record['plan'] }}</td>
                                        <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $record['branches'] ?? '0' }}</td>
                                        <td class="px-4 py-3.5">
                                            <x-admin.badge :tone="$statusTone($record['status'])">{{ $record['status'] }}</x-admin.badge>
                                        </td>
                                        <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $record['owner'] }}</td>
                                        <td class="px-4 py-3.5 text-gray-500">{{ $record['created'] }}</td>
                                    <td class="px-4 py-3.5 text-right">
                                        <x-admin.dropdown>
                                            <x-slot:trigger>
                                                <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">
                                                    <x-admin.icon name="more" />
                                                </button>
                                            </x-slot:trigger>
                                            <a href="{{ route('super-admin.organizations.show', $record['id']) }}" class="block px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-800">View</a>
                                            <a href="{{ route('super-admin.organizations.edit', $record['id']) }}" class="block px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-800">Edit</a>
                                            <form method="POST" action="{{ route('super-admin.organizations.activate', $record['id']) }}">
                                                @csrf
                                                <button type="submit" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-800">Activate</button>
                                            </form>
                                            <form method="POST" action="{{ route('super-admin.organizations.suspend', $record['id']) }}">
                                                @csrf
                                                <button type="submit" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-800">Suspend</button>
                                            </form>
                                            <form method="POST" action="{{ route('super-admin.organizations.login-as', $record['id']) }}">
                                                @csrf
                                                <button type="submit" class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50 dark:hover:bg-gray-800">Login As Business</button>
                                            </form>
                                            <form method="POST" action="{{ route('super-admin.organizations.destroy', $record['id']) }}" onsubmit="return confirm('Delete this business?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">Delete</button>
                                            </form>
                                        </x-admin.dropdown>
                                    </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <x-admin.pagination :paginator="$paginator ?? null" />
            </div>
        @endif

    @elseif (($layout ?? 'table') === 'users')
        @if (count($records ?? []) === 0)
            <x-admin.empty-state title="No users found" description="Invite or seed users to populate this directory." />
        @else
            <div class="admin-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50/90 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500 dark:border-gray-700 dark:bg-gray-800/80 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">User</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Created</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($records as $record)
                                <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <x-admin.avatar :name="$record['name']" />
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $record['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $record['email'] }}</td>
                                    <td class="px-4 py-3.5"><x-admin.badge tone="info">{{ $record['role'] }}</x-admin.badge></td>
                                    <td class="px-4 py-3.5"><x-admin.badge :tone="$statusTone($record['status'])">{{ $record['status'] }}</x-admin.badge></td>
                                    <td class="px-4 py-3.5 text-gray-500">{{ $record['created'] }}</td>
                                    <td class="px-4 py-3.5 text-right">
                                        @if (! empty($record['id']))
                                            <x-admin.button size="sm" variant="ghost" :href="route('super-admin.users.show', $record['id'])">Manage</x-admin.button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <x-admin.pagination :paginator="$paginator ?? null" />
        @endif

    @else
        @if (count($rows ?? []) === 0)
            <x-admin.empty-state title="No records yet" description="Live database records for this module will appear here." />
        @else
            <x-admin.table :columns="$columns" :rows="$rows" :raw-html="$rawHtml ?? false" />
            <x-admin.pagination :paginator="$paginator ?? null" />
        @endif
    @endif
</x-layouts.admin>
