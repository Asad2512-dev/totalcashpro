<x-layouts.auth title="Forgot password" :seo="['title' => 'Forgot password — TotalCashPro']">
    <div class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-md rounded-[1.75rem] border border-gray-200 bg-white p-8 shadow-soft">
            <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center" aria-label="{{ brand_name() }} home">
                <x-brand-logo height="h-9" class="max-w-none" />
            </a>
            <h1 class="mt-8 font-display text-3xl font-extrabold tracking-tight text-gray-900">Reset your password</h1>
            <p class="mt-3 text-sm leading-relaxed text-gray-500">
                Password reset will be connected in Phase 2. For now, contact support if you need access restored.
            </p>
            <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="email" class="text-sm font-semibold text-gray-900">Email</label>
                    <input id="email" name="email" type="email" required class="mt-2 w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm outline-none focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
                </div>
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-primary-600 px-6 py-3.5 text-base font-semibold text-white hover:bg-primary-700">
                    Send reset link
                </button>
            </form>
            <a href="{{ route('login') }}" class="mt-6 inline-flex text-sm font-medium text-primary-700 hover:text-primary-800">Back to sign in</a>
        </div>
    </div>
</x-layouts.auth>
