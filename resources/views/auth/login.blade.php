<x-layouts.auth title="Sign in" :seo="['title' => 'Sign in — TotalCashPro', 'description' => 'Sign in to TotalCashPro as Super Admin, Admin or Staff.']">
    <div class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden overflow-hidden auth-shell text-white lg:flex lg:flex-col lg:justify-between lg:p-12 xl:p-16">
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center drop-shadow-md" aria-label="{{ brand_name() }} home">
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
                    Sign in as Super Admin, Business Admin, or Staff — your role opens the right panel.
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
                >
                    @csrf
                    <div>
                        <label for="email" class="text-sm font-semibold text-gray-900">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
                        @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-sm font-semibold text-gray-900">Password</label>
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-700 hover:text-primary-800">Forgot password?</a>
                        </div>
                        <div class="relative mt-2" x-data="{ showPassword: false }">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20"
                            >
                            <button
                                type="button"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 transition hover:text-gray-600"
                                @click="showPassword = !showPassword"
                                x-bind:aria-label="showPassword ? 'Hide password' : 'Show password'"
                            >
                                <svg x-show="!showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        @error('password') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                        Remember me
                    </label>
                    <button
                        type="submit"
                        class="btn-ripple inline-flex w-full min-h-[52px] items-center justify-center rounded-2xl bg-primary-600 px-6 py-3.5 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-primary-700 disabled:pointer-events-none disabled:translate-y-0 disabled:opacity-80"
                        x-bind:disabled="$store.authUi.submitting"
                    >
                        Sign In
                    </button>
                </form>

                <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-500">
                    <p class="font-semibold text-gray-900">Need an account?</p>
                    <p class="mt-1">New to {{ brand_name() }}? <a href="{{ route('register') }}" class="font-medium text-primary-700 hover:text-primary-800">Start your free trial</a> and create your business in minutes.</p>
                </div>

                <p class="mt-10 text-center text-xs text-gray-400">
                    © {{ date('Y') }} TotalCashPro · <a href="{{ route('home') }}" class="hover:text-gray-600">Marketing site</a>
                </p>
            </div>
        </section>
    </div>
</x-layouts.auth>
