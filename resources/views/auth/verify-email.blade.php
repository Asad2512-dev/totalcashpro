<x-layouts.auth title="Verify email" :seo="['title' => 'Verify Email — TotalCashPro']">
    <div class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="w-full max-w-md rounded-3xl border border-gray-200 bg-white p-8 shadow-soft">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-primary-600">Almost there</p>
            <h1 class="mt-3 font-display text-2xl font-extrabold text-gray-900">Verify your email</h1>
            <p class="mt-3 text-sm leading-relaxed text-gray-500">
                We sent a verification link to <strong class="text-gray-900">{{ auth()->user()->email }}</strong>.
                Please check your inbox and click the link to secure your account.
            </p>

            @if (session('status') === 'verification-link-sent')
                <div class="mt-6 rounded-2xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm text-primary-800">
                    A fresh verification link has been sent.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
                @csrf
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white hover:bg-primary-700">
                    Resend verification email
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                <a href="{{ route('business-admin.onboarding') }}" class="font-medium text-primary-700 hover:text-primary-800">Continue setup</a>
                ·
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="font-medium text-gray-600 hover:text-gray-800">Sign out</a>
            </p>
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
        </div>
    </div>
</x-layouts.auth>
