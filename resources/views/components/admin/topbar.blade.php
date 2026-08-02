@props([
    'title' => 'Dashboard',
    'user' => null,
])

<header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-4 border-b border-gray-200 bg-white/90 px-4 backdrop-blur dark:border-gray-800 dark:bg-gray-900/90 sm:px-6">
    <div class="flex items-center gap-3">
        <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 lg:hidden dark:border-gray-700" @click="sidebarOpen = !sidebarOpen" aria-label="Open menu">
            <x-admin.icon name="menu" />
        </button>
        <button
            type="button"
            class="hidden h-10 w-10 items-center justify-center rounded-xl border border-gray-200 text-gray-600 transition hover:bg-gray-50 lg:inline-flex dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
            @click="collapsed = !collapsed"
            :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            aria-label="Toggle sidebar"
        >
            <x-admin.icon name="panel" />
        </button>
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-gray-400">Super Admin</p>
            <h1 class="font-display text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button type="button" class="hidden rounded-xl border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 sm:inline-flex dark:border-gray-700 dark:text-gray-300" @click="commandOpen = true">
            Quick navigation
        </button>
        <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700" @click="dark = !dark" aria-label="Toggle theme">
            <span x-show="!dark"><x-admin.icon name="moon" /></span>
            <span x-cloak x-show="dark"><x-admin.icon name="sun" /></span>
        </button>

        <x-admin.profile-menu :user="$user" />
    </div>
</header>
