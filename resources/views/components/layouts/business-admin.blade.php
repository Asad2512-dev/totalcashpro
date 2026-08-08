@props([
    'title' => 'Dashboard',
    'active' => 'dashboard',
])

@php
    $ui = app(\App\Services\BusinessAdmin\BusinessAdminUiService::class);
    $nav = $ui->navigation();
    $mobileNav = $ui->mobileNavigation();
    $mobileMore = $ui->mobileMoreNavigation();
    $commandLinks = $ui->commandLinks();
    $user = auth()->user();
    $branches = $ui->branchesFor($user);
    $selectedBranchId = $ui->selectedBranchId($user);
    $organization = $user->organization;
    $unreadNotifications = $user
        ? \App\Models\AppNotification::query()->where('user_id', $user->id)->whereNull('read_at')->count()
        : 0;
    $impersonation = app(\App\Services\SuperAdmin\ImpersonationService::class)->context(request());
@endphp

<!DOCTYPE html>
<html lang="en" class="admin-panel" x-data="adminShell">
<head>
    <meta charset="utf-8">
    <x-theme-init />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#16A34A">
    <title>{{ $title }} — {{ $organization?->name ?? 'Business' }} · {{ brand_name() }}</title>
    <x-brand-favicon />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-shell admin-shell--viewport admin-shell--mobile-nav bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
    <x-admin.impersonation-banner
        :impersonator="$impersonation['impersonator']"
        :organization-name="$impersonation['organization_name']"
        :started-at="$impersonation['started_at']"
        :reason="$impersonation['reason']"
    />
    <div class="admin-shell-layout flex min-h-0">
        <x-admin.sidebar :nav="$nav" :active="$active" :business-tree="[]" home-route="business-admin.dashboard" />

        <div class="flex min-h-0 min-w-0 flex-1 flex-col overflow-hidden transition-[padding] duration-300 lg:pl-72" :class="collapsed ? 'lg:pl-[5.25rem]' : 'lg:pl-72'">
            <x-admin.topbar
                :title="$title"
                :user="$user"
                panel-label="{{ $organization?->name ?? 'Business Admin' }}"
                profile-route="business-admin.profile"
                settings-route="business-admin.settings"
                security-route="business-admin.security.index"
                notifications-route="business-admin.notifications"
                :unread-notifications="$unreadNotifications"
            >
                <x-slot:actions>
                    <x-admin.branch-selector
                        :branches="$branches"
                        :selected-branch-id="$selectedBranchId"
                        :action="route('business-admin.branch.select')"
                        compact
                        silent
                        class="hidden min-w-0 max-w-[11rem] sm:flex md:hidden"
                    />
                    <x-admin.branch-filter compact class="hidden min-w-0 md:flex" />
                </x-slot:actions>
            </x-admin.topbar>

            @if ($branches->count() > 1)
                <div class="border-b border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900 sm:hidden">
                    <x-admin.branch-selector
                        :branches="$branches"
                        :selected-branch-id="$selectedBranchId"
                        :action="route('business-admin.branch.select')"
                        compact
                        silent
                        class="w-full"
                    />
                </div>
            @endif

            <main class="admin-shell-main admin-page admin-fade-in flex-1 px-4 py-3 sm:px-6 sm:py-5 lg:px-8 lg:py-6">
                @if ($user && ! $user->hasVerifiedEmail())
                    <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-900/40 dark:bg-amber-900/20 dark:text-amber-100">
                        Please verify your email address.
                        <a href="{{ route('verification.notice') }}" class="font-semibold underline">Resend verification link</a>
                    </div>
                @endif
                @if (session('status'))
                    <div class="mb-5">
                        <x-admin.alert tone="success">{{ session('status') }}</x-admin.alert>
                    </div>
                    <x-admin.toast tone="success" :message="session('status')" />
                @endif
                @if ($errors->any())
                    <div class="mb-5">
                        <x-admin.alert tone="danger">{{ $errors->first() }}</x-admin.alert>
                    </div>
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
                    class="w-full border-0 bg-transparent text-sm outline-none focus:ring-0 dark:text-white"
                    placeholder="Jump to Cash Up, Staff, Inventory…"
                >
            </div>
            <div class="max-h-80 space-y-1 overflow-y-auto p-2 text-sm">
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

    <x-admin.mobile-nav :items="$mobileNav" :more-groups="$mobileMore" :active="$active" />

    <x-admin.logout-confirm :user-name="$user?->name" />
</body>
</html>
