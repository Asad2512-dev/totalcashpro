@props([
    'userName' => null,
])

<div
    x-cloak
    x-show="logoutConfirm"
    x-transition.opacity
    class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/50 p-4 backdrop-blur-[2px]"
    @keydown.escape.window="logoutConfirm && cancelLogout()"
    @click.self="cancelLogout()"
    role="dialog"
    aria-modal="true"
    aria-labelledby="logout-confirm-title"
>
    <div
        x-show="logoutConfirm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
        class="w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-soft dark:border-gray-700 dark:bg-gray-900"
        @click.stop
    >
        <div class="px-6 pt-6">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400">
                <x-admin.icon name="logout" class="h-5 w-5" />
            </div>
            <h3 id="logout-confirm-title" class="mt-4 text-center font-display text-xl font-bold text-gray-900 dark:text-white">
                Sign out?
            </h3>
            <p class="mt-2 text-center text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                @if ($userName)
                    You’re signed in as <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $userName }}</span>.
                    Are you sure you want to log out of TotalCashPro?
                @else
                    Are you sure you want to log out of TotalCashPro?
                @endif
            </p>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3 border-t border-gray-100 bg-gray-50/80 px-6 py-4 dark:border-gray-800 dark:bg-gray-950/40">
            <button
                type="button"
                class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                @click="cancelLogout()"
            >
                Stay signed in
            </button>
            <form method="POST" action="{{ route('logout') }}" class="contents">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500/30"
                >
                    Yes, log out
                </button>
            </form>
        </div>
    </div>
</div>
