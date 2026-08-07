@props([
    'code' => '404',
    'title' => 'Page not found',
    'message' => 'The page you requested could not be found.',
])

<!DOCTYPE html>
<html lang="en" class="admin-panel">
<head>
    <meta charset="utf-8">
    <x-theme-init />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#16A34A">
    <title>{{ $code }} — {{ $title }} · {{ brand_name() }}</title>
    <x-brand-favicon />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-shell flex min-h-screen flex-col bg-gray-50 text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
    <header class="border-b border-gray-200 bg-white/90 px-4 py-4 backdrop-blur dark:border-gray-800 dark:bg-gray-900/90 sm:px-6">
        <a href="{{ route('home') }}" class="inline-flex items-center" aria-label="{{ brand_name() }} home">
            <x-brand-logo height="h-8" class="max-w-none" />
        </a>
    </header>

    <main class="flex flex-1 items-center justify-center px-4 py-12 sm:px-6">
        <div class="admin-fade-in w-full max-w-lg text-center">
            <p class="font-display text-6xl font-extrabold tracking-tight text-primary-600 sm:text-7xl">{{ $code }}</p>
            <h1 class="mt-4 font-display text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">{{ $title }}</h1>
            <p class="mt-3 text-sm leading-relaxed text-gray-500 dark:text-gray-400 sm:text-base">{{ $message }}</p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <x-admin.button :href="url()->previous() !== url()->current() ? url()->previous() : route('home')" variant="secondary">
                    Go back
                </x-admin.button>
                @auth
                    <x-admin.button :href="match (true) {
                        auth()->user()?->isSuperAdmin() => route('super-admin.dashboard'),
                        auth()->user()?->isAdmin() => route('business-admin.dashboard'),
                        auth()->user()?->isStaff() => route('staff.dashboard'),
                        default => route('home'),
                    }">
                        Open dashboard
                    </x-admin.button>
                @else
                    <x-admin.button :href="route('login')">Sign in</x-admin.button>
                @endauth
            </div>
        </div>
    </main>
</body>
</html>
