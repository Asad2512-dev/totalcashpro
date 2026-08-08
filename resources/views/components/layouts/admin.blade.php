@props([
    'title' => 'Dashboard',
    'active' => 'dashboard',
])

@php
    $ui = app(\App\Services\SuperAdmin\SuperAdminUiService::class);
    $nav = $ui->navigation();
    $businessTree = $ui->businessTree();
    $commandLinks = $ui->commandLinks();
    $user = auth()->user();
    $unreadNotifications = $user
        ? \App\Models\AppNotification::query()->where('user_id', $user->id)->whereNull('read_at')->count()
        : 0;
@endphp

<!DOCTYPE html>
<html lang="en" class="admin-panel" x-data="adminShell">
<head>
    <meta charset="utf-8">
    <x-theme-init />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#16A34A">
    <title>{{ $title }} — Super Admin · {{ brand_name() }}</title>
    <x-brand-favicon />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-shell admin-shell--viewport bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
    <div class="admin-shell-layout flex min-h-0">
        <x-admin.sidebar :nav="$nav" :active="$active" :business-tree="$businessTree" />

        <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden transition-[padding] duration-300 lg:pl-72" :class="collapsed ? 'lg:pl-[5.25rem]' : 'lg:pl-72'">
            <x-admin.topbar
                :title="$title"
                :user="$user"
                profile-route="super-admin.profile.edit"
                settings-route="super-admin.settings"
                security-route="super-admin.security.index"
                notifications-route="super-admin.notifications"
                :unread-notifications="$unreadNotifications"
            />

            <main class="admin-shell-main admin-page admin-fade-in flex-1 px-4 py-4 sm:px-6 sm:py-5 lg:px-8 lg:py-6">
                @if (session('status'))
                    <div class="mb-5">
                        <x-admin.alert tone="success">{{ session('status') }}</x-admin.alert>
                    </div>
                    <x-admin.toast tone="success" :message="session('status')" />
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>

    <div
        x-cloak
        x-show="commandOpen"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-start justify-center bg-gray-900/40 px-3 pt-16 sm:px-4 sm:pt-24"
        @keydown.escape.window="commandOpen = false"
        @click.self="commandOpen = false"
    >
        <div class="admin-slide-up w-full max-w-xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-soft dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                <input
                    type="text"
                    x-model="commandQuery"
                    x-ref="commandInput"
                    x-init="$watch('commandOpen', value => value && $nextTick(() => $refs.commandInput.focus()))"
                    @input.debounce.300ms="
                        if (commandQuery.length < 2) { searchResults = []; return; }
                        fetch(@js(route('super-admin.search')) + '?q=' + encodeURIComponent(commandQuery))
                            .then(r => r.json())
                            .then(data => searchResults = data.results || [])
                            .catch(() => searchResults = [])
                    "
                    class="w-full border-0 bg-transparent text-sm outline-none focus:ring-0 dark:text-white"
                    placeholder="Search businesses, users, plans, tickets…"
                >
            </div>
            <div class="max-h-80 space-y-1 overflow-y-auto p-2 text-sm">
                <template x-if="searchResults.length">
                    <div class="mb-2 space-y-1">
                        <template x-for="result in searchResults" :key="result.url">
                            <a :href="result.url" class="block rounded-xl px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800" @click="commandOpen = false">
                                <span class="text-[11px] font-semibold uppercase tracking-wide text-gray-400" x-text="result.group"></span>
                                <span class="mt-0.5 block" x-text="result.label"></span>
                            </a>
                        </template>
                    </div>
                </template>
                @foreach ($commandLinks as $link)
                    <a
                        href="{{ route($link['route']) }}"
                        x-show="!commandQuery || @js(strtolower($link['label'])).includes(commandQuery.toLowerCase())"
                        class="block rounded-xl px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800"
                        @click="commandOpen = false"
                    >{{ $link['label'] }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <x-admin.mobile-backdrop />

    <x-admin.logout-confirm :user-name="$user?->name" />
</body>
</html>
