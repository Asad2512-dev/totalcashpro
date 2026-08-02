@props([
    'title' => 'Dashboard',
    'user' => null,
    'panelLabel' => 'Super Admin',
    'profileRoute' => 'super-admin.profile',
    'settingsRoute' => 'super-admin.settings',
])

<header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-4 border-b border-gray-200 bg-white/90 px-4 backdrop-blur dark:border-gray-800 dark:bg-gray-900/90 sm:px-6">
    <div class="flex min-w-0 items-center gap-3">
        <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 lg:hidden dark:border-gray-700" @click="sidebarOpen = !sidebarOpen" aria-label="Open menu">
            <x-admin.icon name="menu" />
        </button>
        <div class="min-w-0">
            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-gray-400">{{ $panelLabel }}</p>
            <h1 class="truncate font-display text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
        </div>
    </div>

    <div class="flex items-center gap-2">
        {{ $actions ?? '' }}
        <button type="button" class="hidden rounded-xl border border-gray-200 px-3 py-2 text-sm font-medium text-gray-600 sm:inline-flex dark:border-gray-700 dark:text-gray-300" @click="commandOpen = true">
            Quick navigation
        </button>
        <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700" @click="dark = !dark" aria-label="Toggle theme">
            <span x-show="!dark"><x-admin.icon name="moon" /></span>
            <span x-cloak x-show="dark"><x-admin.icon name="sun" /></span>
        </button>

        <x-admin.profile-menu :user="$user" :profile-route="$profileRoute" :settings-route="$settingsRoute" />
    </div>
</header>
