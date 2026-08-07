<x-layouts.auth title="Two-factor authentication" :seo="['title' => 'Verify sign-in — TotalCashPro']">
    <div class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">
            <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center" aria-label="{{ brand_name() }} home">
                <x-brand-logo height="h-9" class="max-w-none" />
            </a>

            <h1 class="mt-8 font-display text-2xl font-bold text-gray-900">Verify your sign-in</h1>
            <p class="mt-2 text-sm text-gray-600">Enter the 6-digit code sent to your email, or use a recovery code.</p>

            @if (session('status'))
                <p class="mt-4 rounded-xl bg-primary-50 px-4 py-3 text-sm text-primary-800">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700">Verification code</label>
                    <input id="otp" name="otp" type="text" inputmode="numeric" maxlength="6" required autocomplete="one-time-code"
                        class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 text-center text-lg tracking-widest focus:border-primary-500 focus:ring-primary-500" />
                    @error('otp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-primary w-full">Continue</button>
            </form>

            <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Resend code</button>
            </form>
        </div>
    </div>
</x-layouts.auth>
