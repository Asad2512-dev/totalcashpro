@props([
    'user' => null,
    'profileRoute' => 'super-admin.profile',
    'settingsRoute' => 'super-admin.settings',
])

<x-admin.dropdown align="right" width="w-56">
    <x-slot:trigger>
        <button type="button" class="admin-touch-target flex items-center gap-2 rounded-xl border border-gray-200 px-2 py-1.5 transition hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/40 dark:border-gray-700 dark:hover:bg-gray-800">
            <x-admin.avatar :name="$user?->name ?? 'Admin'" />
            <div class="hidden text-left sm:block">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user?->name ?? 'Admin' }}</p>
                <p class="text-[11px] text-gray-500">{{ $user?->email ?? '' }}</p>
            </div>
        </button>
    </x-slot:trigger>

    <a href="{{ route($profileRoute) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-800">Profile</a>
    <a href="{{ route($settingsRoute) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-800">Settings</a>
    <button
        type="button"
        class="block w-full px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30"
        @click="open = false; $dispatch('confirm-logout')"
    >
        Logout
    </button>
</x-admin.dropdown>
