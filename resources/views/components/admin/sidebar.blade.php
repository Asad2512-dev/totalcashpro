@props([
    'nav' => [],
    'active' => 'dashboard',
    'businessTree' => [],
    'homeRoute' => 'super-admin.dashboard',
])

@php
    $currentOrganizationId = (int) (optional(request()->route('organization'))->id ?? request()->route('organization') ?? 0);
    $currentBranchId = (int) (optional(request()->route('branch'))->id ?? request()->route('branch') ?? 0);
    $businessesSectionOpen = in_array($active, ['businesses', 'organizations'], true)
        || request()->routeIs('super-admin.organizations.*')
        || request()->routeIs('super-admin.branches.*');
@endphp

<aside
    id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-40 flex w-[min(100vw-3rem,18rem)] flex-col border-r border-gray-200 bg-white transition-transform duration-300 ease-out dark:border-gray-800 dark:bg-gray-900 sm:w-72"
    :class="{
        'w-[5.25rem]': collapsed,
        'w-[min(100vw-3rem,18rem)] sm:w-72': !collapsed,
        'translate-x-0': sidebarOpen,
        '-translate-x-full lg:translate-x-0': !sidebarOpen
    }"
>
    <div
        class="flex h-16 shrink-0 items-center border-b border-gray-200 px-3 dark:border-gray-800"
        :class="collapsed ? 'justify-center px-2' : 'justify-start px-3'"
    >
        <a href="{{ route($homeRoute) }}" class="inline-flex min-w-0 items-center overflow-hidden rounded-lg">
            <span class="inline-flex" x-bind:class="collapsed ? 'hidden' : ''">
                <x-brand-logo height="h-8" class="max-w-none" />
            </span>
            <span
                class="hidden h-9 w-9 items-center justify-center rounded-xl bg-primary-600 text-sm font-bold text-white"
                x-bind:class="collapsed ? '!inline-flex' : 'hidden'"
            >T</span>
        </a>
    </div>

    <div class="shrink-0 border-b border-gray-200 px-2 py-3 dark:border-gray-800" x-bind:class="collapsed ? 'hidden' : ''">
        <button type="button" @click="commandOpen = true; closeSidebar()" class="admin-touch-target flex w-full min-h-[44px] items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-left text-sm text-gray-500 transition hover:border-primary-300 dark:border-gray-700 dark:bg-gray-800">
            <span class="inline-flex min-w-0 items-center gap-2 truncate"><x-admin.icon name="search" class="h-4 w-4 shrink-0" /> Search…</span>
            <kbd class="shrink-0 rounded-md border border-gray-200 bg-white px-1.5 py-0.5 text-[10px] font-semibold dark:border-gray-600 dark:bg-gray-900">⌘K</kbd>
        </button>
    </div>

    <nav class="min-h-0 flex-1 space-y-4 overflow-y-auto overflow-x-hidden px-2 py-4">
        @foreach ($nav as $group)
            <div>
                <p
                    class="admin-sidebar-group-label"
                    x-bind:class="collapsed ? 'hidden' : ''"
                >{{ $group['label'] }}</p>
                <ul class="mt-1.5 space-y-0.5">
                    @foreach ($group['items'] as $item)
                        @php
                            $routeKey = \Illuminate\Support\Str::afterLast($item['route'], '.');
                            if (str_starts_with($item['route'], 'super-admin.')) {
                                $routeKey = str_replace('super-admin.', '', $item['route']);
                            } elseif (str_starts_with($item['route'], 'business-admin.')) {
                                $routeKey = str_replace('business-admin.', '', $item['route']);
                            } elseif (str_starts_with($item['route'], 'staff.')) {
                                $routeKey = str_replace('staff.', '', $item['route']);
                            }
                            $isActive = $active === $routeKey
                                || $active === \Illuminate\Support\Str::afterLast($item['route'], '.')
                                || ($active === 'dashboard' && $routeKey === 'dashboard')
                                || ($routeKey === 'businesses' && $businessesSectionOpen);
                            $isBusinesses = $routeKey === 'businesses';
                            $businessesActive = $isBusinesses && ($active === 'businesses' || $active === 'organizations');
                        @endphp
                        <li @if ($isBusinesses) x-data="{ businessesOpen: {{ $businessesSectionOpen ? 'true' : 'false' }} }" @endif>
                            <div class="flex items-stretch gap-0.5">
                                <a
                                    href="{{ route($item['route']) }}"
                                    title="{{ $item['label'] }}"
                                    @click="closeSidebar()"
                                    @class([
                                        'admin-sidebar-link group min-w-0',
                                        'bg-primary-50 text-primary-700 dark:bg-primary-900/25 dark:text-primary-300' => ($isActive && ! $isBusinesses) || $businessesActive,
                                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800' => ! (($isActive && ! $isBusinesses) || $businessesActive),
                                    ])
                                    :class="collapsed ? 'justify-center px-2' : ''"
                                >
                                    <x-admin.icon :name="$item['icon']" class="h-4 w-4 shrink-0" />
                                    <span class="truncate" x-bind:class="collapsed ? 'hidden' : ''">{{ $item['label'] }}</span>
                                </a>
                                @if ($isBusinesses && count($businessTree) > 0)
                                    <button
                                        type="button"
                                        class="admin-touch-target inline-flex shrink-0 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-50 hover:text-gray-700 dark:hover:bg-gray-800"
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
                                    class="mt-1 space-y-0.5 border-l border-gray-200 pl-2 ml-3 dark:border-gray-700"
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
                                            <div class="flex items-stretch gap-0.5">
                                                <a
                                                    href="{{ $business['url'] }}"
                                                    @click="closeSidebar()"
                                                    @class([
                                                        'admin-sidebar-sublink min-w-0 truncate',
                                                        'bg-primary-50 text-primary-700 dark:bg-primary-900/25 dark:text-primary-300' => $currentOrganizationId === (int) $business['id'],
                                                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800' => $currentOrganizationId !== (int) $business['id'],
                                                    ])
                                                    title="{{ $business['name'] }}"
                                                >{{ $business['name'] }}</a>
                                                @if (count($business['branches']) > 0)
                                                    <button
                                                        type="button"
                                                        class="admin-touch-target inline-flex shrink-0 items-center justify-center rounded-md text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800"
                                                        @click="open = !open"
                                                        aria-label="Toggle branches"
                                                    >
                                                        <x-admin.icon name="chevron" class="h-3.5 w-3.5 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                                                    </button>
                                                @endif
                                            </div>
                                            @if (count($business['branches']) > 0)
                                                <ul class="mt-0.5 space-y-0.5 border-l border-gray-200 pl-2 ml-2 dark:border-gray-700" x-show="open" x-cloak>
                                                    @foreach ($business['branches'] as $branch)
                                                        <li>
                                                            <a
                                                                href="{{ $branch['url'] }}"
                                                                @click="closeSidebar()"
                                                                @class([
                                                                    'admin-sidebar-nested-link truncate',
                                                                    'bg-primary-50 font-medium text-primary-700 dark:bg-primary-900/25 dark:text-primary-300' => $currentBranchId === (int) $branch['id'],
                                                                    'text-gray-500 hover:bg-primary-50 hover:text-primary-700 dark:hover:bg-primary-900/20 dark:hover:text-primary-300' => $currentBranchId !== (int) $branch['id'],
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

    <div class="shrink-0 border-t border-gray-200 p-2 dark:border-gray-800">
        <button
            type="button"
            class="admin-sidebar-link w-full text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800"
            :class="collapsed ? 'justify-center px-2' : ''"
            @click="requestLogout()"
        >
            <x-admin.icon name="logout" class="h-4 w-4 shrink-0" />
            <span x-bind:class="collapsed ? 'hidden' : ''">Logout</span>
        </button>
    </div>
</aside>
