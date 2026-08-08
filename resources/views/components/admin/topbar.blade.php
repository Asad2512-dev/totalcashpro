@props([
    'title' => 'Dashboard',
    'user' => null,
    'panelLabel' => 'Super Admin',
    'profileRoute' => 'super-admin.profile',
    'settingsRoute' => 'super-admin.settings',
    'securityRoute' => 'super-admin.security.index',
    'notificationsRoute' => null,
    'unreadNotifications' => 0,
])

<header class="sticky top-0 z-30 border-b border-gray-200 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-gray-900/90">
    <div class="flex h-16 items-center justify-between gap-2 px-3 sm:gap-4 sm:px-6">
        <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
            <button
                type="button"
                class="admin-touch-target inline-flex shrink-0 items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 lg:hidden"
                @click="toggleSidebar()"
                :aria-expanded="sidebarOpen.toString()"
                aria-controls="admin-sidebar"
                aria-label="Open menu"
            >
                <x-admin.icon name="menu" />
            </button>
            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-semibold uppercase tracking-[0.12em] text-gray-400 sm:text-[11px]">{{ $panelLabel }}</p>
                <h1 class="truncate font-display text-lg font-bold text-gray-900 dark:text-white sm:text-xl">{{ $title }}</h1>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
            {{ $actions ?? '' }}
            <button
                type="button"
                class="admin-touch-target hidden items-center justify-center rounded-xl border border-gray-200 px-3 text-sm font-medium text-gray-600 md:inline-flex dark:border-gray-700 dark:text-gray-300"
                @click="commandOpen = true"
            >
                <span class="hidden lg:inline">Quick navigation</span>
                <span class="lg:hidden">Navigate</span>
            </button>
            <button
                type="button"
                class="admin-touch-target inline-flex items-center justify-center rounded-xl border border-gray-200 md:hidden dark:border-gray-700"
                @click="commandOpen = true"
                aria-label="Quick navigation"
            >
                <x-admin.icon name="search" class="h-4 w-4" />
            </button>
            <x-admin.notification-bell :route="$notificationsRoute" :count="$unreadNotifications" />

            <button
                type="button"
                class="admin-touch-target inline-flex items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700"
                @click="dark = !dark"
                aria-label="Toggle theme"
            >
                <span x-show="!dark"><x-admin.icon name="moon" /></span>
                <span x-cloak x-show="dark"><x-admin.icon name="sun" /></span>
            </button>

            <x-admin.profile-menu :user="$user" :profile-route="$profileRoute" :settings-route="$settingsRoute" :security-route="$securityRoute" />
        </div>
    </div>
</header>
