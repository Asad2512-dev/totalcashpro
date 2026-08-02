@props([
    'nav' => [],
    'active' => 'dashboard',
    'businessTree' => [],
])

@php
    $currentOrganizationId = (int) (optional(request()->route('organization'))->id ?? request()->route('organization') ?? 0);
    $currentBranchId = (int) (optional(request()->route('branch'))->id ?? request()->route('branch') ?? 0);
    $businessesSectionOpen = in_array($active, ['businesses', 'organizations'], true)
        || request()->routeIs('super-admin.organizations.*')
        || request()->routeIs('super-admin.branches.*');
@endphp

<aside
    class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-gray-200 bg-white transition-[width] duration-300 dark:border-gray-800 dark:bg-gray-900 max-lg:translate-x-0"
    :class="{
        'w-[5.25rem]': collapsed,
        'w-72': !collapsed,
        'translate-x-0': sidebarOpen,
        '-translate-x-full lg:translate-x-0': !sidebarOpen
    }"
>
    <div
        class="flex h-16 shrink-0 items-center gap-2 border-b border-gray-200 px-3 dark:border-gray-800"
        :class="collapsed ? 'justify-center px-2' : 'justify-between px-4'"
    >
        <a href="{{ route('super-admin.dashboard') }}" class="inline-flex min-w-0 items-center overflow-hidden rounded-lg dark:bg-white dark:px-2 dark:py-1">
            <span class="inline-flex" x-bind:class="collapsed ? 'hidden' : ''">
                <x-brand-logo height="h-8" class="max-w-none" />
            </span>
            <span
                class="hidden h-9 w-9 items-center justify-center rounded-xl bg-primary-600 text-sm font-bold text-white"
                x-bind:class="collapsed ? '!inline-flex' : 'hidden'"
            >T</span>
        </a>

        <button
            type="button"
            @click="collapsed = !collapsed"
            class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-gray-200 text-gray-600 transition hover:bg-gray-50 lg:inline-flex dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
            :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            aria-label="Toggle sidebar"
        >
            <x-admin.icon name="panel" class="h-4 w-4" />
        </button>
    </div>

    <div class="shrink-0 border-b border-gray-200 px-3 py-3 dark:border-gray-800" x-bind:class="collapsed ? 'hidden' : ''">
        <button type="button" @click="commandOpen = true" class="flex w-full items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-left text-sm text-gray-500 transition hover:border-primary-300 dark:border-gray-700 dark:bg-gray-800">
            <span class="inline-flex items-center gap-2"><x-admin.icon name="search" /> Search…</span>
            <kbd class="rounded-md border border-gray-200 bg-white px-1.5 py-0.5 text-[10px] font-semibold dark:border-gray-600 dark:bg-gray-900">⌘K</kbd>
        </button>
    </div>

    <nav class="min-h-0 flex-1 space-y-5 overflow-y-auto overflow-x-hidden px-3 py-4">
        @foreach ($nav as $group)
            <div>
                <p
                    class="px-3 text-[11px] font-bold uppercase tracking-[0.14em] text-gray-400"
                    x-bind:class="collapsed ? 'hidden' : ''"
                >{{ $group['label'] }}</p>
                <ul class="mt-2 space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @php
                            $routeKey = str_replace('super-admin.', '', $item['route']);
                            $isActive = $active === $routeKey
                                || $active === \Illuminate\Support\Str::afterLast($item['route'], '.')
                                || ($active === 'dashboard' && $routeKey === 'dashboard')
                                || ($routeKey === 'businesses' && $businessesSectionOpen);
                            $isBusinesses = $routeKey === 'businesses';
                        @endphp
                        <li @if ($isBusinesses) x-data="{ businessesOpen: {{ $businessesSectionOpen ? 'true' : 'false' }} }" @endif>
                            <div class="flex items-center gap-1">
                                <a
                                    href="{{ route($item['route']) }}"
                                    title="{{ $item['label'] }}"
                                    @class([
                                        'group flex min-w-0 flex-1 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                                        'bg-primary-50 text-primary-700 dark:bg-primary-900/25 dark:text-primary-300' => $isActive && ! $isBusinesses,
                                        'bg-primary-50 text-primary-700 dark:bg-primary-900/25 dark:text-primary-300' => $isBusinesses && ($active === 'businesses' || $active === 'organizations'),
                                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800' => ! ($isActive && ! $isBusinesses) && ! ($isBusinesses && ($active === 'businesses' || $active === 'organizations')),
                                    ])
                                    :class="collapsed ? 'justify-center px-2' : ''"
                                >
                                    <x-admin.icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                                    <span class="truncate" x-bind:class="collapsed ? 'hidden' : ''">{{ $item['label'] }}</span>
                                </a>
                                @if ($isBusinesses && count($businessTree) > 0)
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-50 hover:text-gray-700 dark:hover:bg-gray-800"
                                        x-bind:class="collapsed ? 'hidden' : ''"
                                        @click="businessesOpen = !businessesOpen"
                                        aria-label="Toggle businesses"
                                    >
                                        <x-admin.icon name="chevron" class="h-4 w-4 transition-transform" x-bind:class="businessesOpen ? 'rotate-180' : ''" />
                                    </button>
                                @endif
                            </div>

                            @if ($isBusinesses && count($businessTree) > 0)
                                <ul
                                    class="mt-1 space-y-1 border-l border-gray-200 pl-3 ml-4 dark:border-gray-700"
                                    x-show="businessesOpen && !collapsed"
                                    x-cloak
                                >
                                    @foreach ($businessTree as $business)
                                        @php
                                            $branchIds = collect($business['branches'])->pluck('id')->all();
                                            $businessOpen = $currentOrganizationId === (int) $business['id']
                                                || in_array($currentBranchId, $branchIds, true);
                                        @endphp
                                        <li x-data="{ open: {{ $businessOpen ? 'true' : 'false' }} }">
                                            <div class="flex items-center gap-1">
                                                <a
                                                    href="{{ $business['url'] }}"
                                                    @class([
                                                        'block min-w-0 flex-1 truncate rounded-lg px-2.5 py-1.5 text-[13px] font-medium',
                                                        'bg-primary-50 text-primary-700' => $currentOrganizationId === (int) $business['id'],
                                                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800' => $currentOrganizationId !== (int) $business['id'],
                                                    ])
                                                    title="{{ $business['name'] }}"
                                                >{{ $business['name'] }}</a>
                                                @if (count($business['branches']) > 0)
                                                    <button
                                                        type="button"
                                                        class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-gray-400 hover:bg-gray-50"
                                                        @click="open = !open"
                                                        aria-label="Toggle branches"
                                                    >
                                                        <x-admin.icon name="chevron" class="h-3.5 w-3.5 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                                                    </button>
                                                @endif
                                            </div>
                                            @if (count($business['branches']) > 0)
                                                <ul class="mt-0.5 space-y-0.5 pl-3" x-show="open" x-cloak>
                                                    @foreach ($business['branches'] as $branch)
                                                        <li>
                                                            <a
                                                                href="{{ $branch['url'] }}"
                                                                @class([
                                                                    'block truncate rounded-lg px-2 py-1 text-xs',
                                                                    'bg-primary-50 text-primary-700 font-medium' => $currentBranchId === (int) $branch['id'],
                                                                    'text-gray-500 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-primary-900/20' => $currentBranchId !== (int) $branch['id'],
                                                                ])
                                                                title="{{ $branch['name'] }}"
                                                            >{{ $branch['name'] }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <div class="shrink-0 space-y-2 border-t border-gray-200 p-3 dark:border-gray-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
                :class="collapsed ? 'justify-center px-2' : ''"
            >
                <x-admin.icon name="logout" class="h-4 w-4 shrink-0" />
                <span x-bind:class="collapsed ? 'hidden' : ''">Logout</span>
            </button>
        </form>
    </div>
</aside>
