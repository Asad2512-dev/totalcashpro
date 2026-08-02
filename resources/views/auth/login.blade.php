<x-layouts.auth title="Sign in" :seo="['title' => 'Sign in — TotalCashPro', 'description' => 'Sign in to TotalCashPro as Super Admin, Admin or Staff.']">
    <div class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden overflow-hidden auth-shell text-white lg:flex lg:flex-col lg:justify-between lg:p-12 xl:p-16">
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center rounded-2xl bg-white px-3 py-2" aria-label="{{ brand_name() }} home">
                    <x-brand-logo height="h-9" class="max-w-none" />
                </a>
                <h1 class="mt-14 max-w-md font-display text-4xl font-extrabold tracking-tight xl:text-5xl">
                    One secure login for your whole operation
                </h1>
                <p class="mt-5 max-w-md text-base leading-relaxed text-white/75">
                    Super Admin, business Admin and Staff share the same sign-in. Your role is applied after authentication.
                </p>
            </div>

            <div class="relative z-10 mt-12 space-y-4">
                <div class="glass-panel rounded-3xl p-5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-primary-200">Today’s overview</p>
                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <div class="rounded-2xl bg-white/10 p-3">
                            <p class="text-xs text-white/60">Cash up</p>
                            <p class="mt-1 font-display text-xl font-bold">£1,942</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-3">
                            <p class="text-xs text-white/60">Staff</p>
                            <p class="mt-1 font-display text-xl font-bold">18</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-3">
                            <p class="text-xs text-white/60">Alerts</p>
                            <p class="mt-1 font-display text-xl font-bold">05</p>
                        </div>
                    </div>
                </div>
                <ul class="space-y-3 text-sm text-white/80">
                    <li class="flex gap-2"><span class="text-primary-300">✓</span> Secure cloud access for restaurants and retail</li>
                    <li class="flex gap-2"><span class="text-primary-300">✓</span> Cash up, attendance, inventory and reports</li>
                    <li class="flex gap-2"><span class="text-primary-300">✓</span> Role-based access for owners, managers and staff</li>
                </ul>
            </div>
        </section>

        <section class="flex items-center justify-center px-6 py-12 sm:px-10">
            <div class="w-full max-w-md">
                <div class="mb-10 lg:hidden">
                    <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center" aria-label="{{ brand_name() }} home">
                        <x-brand-logo height="h-9" class="max-w-none" />
                    </a>
                </div>

                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-primary-600">Welcome back</p>
                <h2 class="mt-3 font-display text-3xl font-extrabold tracking-tight text-gray-900">Sign in to your account</h2>
                <p class="mt-3 text-sm leading-relaxed text-gray-500">
                    Phase 2.1: Super Admin sign-in only. Use your operator credentials to open the CMS.
                </p>

                @if (session('status'))
                    <div class="mt-6 rounded-2xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm text-primary-800">
                        {{ session('status') }}
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('login.attempt') }}"
                    class="mt-8 space-y-5"
                    x-data="{ loading: false }"
                    @submit="loading = true"
                >
                    @csrf
                    <div>
                        <label for="email" class="text-sm font-semibold text-gray-900">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" :disabled="loading" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 disabled:opacity-60">
                        @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-sm font-semibold text-gray-900">Password</label>
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-700 hover:text-primary-800">Forgot password?</a>
                        </div>
                        <input id="password" name="password" type="password" required autocomplete="current-password" :disabled="loading" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 disabled:opacity-60">
                        @error('password') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" value="1" :disabled="loading" class="rounded border-gray-300 text-primary-600 focus:ring-primary-600 disabled:opacity-60">
                        Remember me
                    </label>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="btn-ripple inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary-600 px-6 py-3.5 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-primary-700 disabled:pointer-events-none disabled:opacity-80"
                    >
                        <span
                            x-cloak
                            x-show="loading"
                            class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"
                            aria-hidden="true"
                        ></span>
                        <span x-text="loading ? 'Signing in…' : 'Sign In'">Sign In</span>
                    </button>

                    <div
                        x-cloak
                        x-show="loading"
                        x-transition.opacity
                        class="auth-loader-overlay"
                        role="status"
                        aria-live="polite"
                        aria-busy="true"
                    >
                        <div class="auth-loader-panel">
                            <div class="auth-loader-spinner" aria-hidden="true"></div>
                            <div class="text-center">
                                <p class="font-display text-base font-bold text-gray-900">Signing you in</p>
                                <p class="mt-1 text-sm text-gray-500">Please wait a moment…</p>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-500">
                    <p class="font-semibold text-gray-900">Need an account?</p>
                    <p class="mt-1">There is no public registration. <a href="{{ route('request-access') }}" class="font-medium text-primary-700 hover:text-primary-800">Request a demo</a> and our team will create your organisation.</p>
                </div>

                <p class="mt-10 text-center text-xs text-gray-400">
                    © {{ date('Y') }} TotalCashPro · <a href="{{ route('home') }}" class="hover:text-gray-600">Marketing site</a>
                </p>
            </div>
        </section>
    </div>
</x-layouts.auth>
