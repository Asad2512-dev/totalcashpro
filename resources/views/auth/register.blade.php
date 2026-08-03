<x-layouts.auth title="Start free trial" :seo="['title' => 'Start Your Free Trial — TotalCashPro', 'description' => 'Create your TotalCashPro account and start a 14-day Professional trial instantly.']">
    <div class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden overflow-hidden auth-shell text-white lg:flex lg:flex-col lg:justify-between lg:p-12 xl:p-16">
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center drop-shadow-md" aria-label="{{ brand_name() }} home">
                    <x-brand-logo height="h-9" class="max-w-none" />
                </a>
                <h1 class="mt-14 max-w-md font-display text-4xl font-extrabold tracking-tight xl:text-5xl">
                    Start your 14-day Professional trial
                </h1>
                <p class="mt-5 max-w-md text-base leading-relaxed text-white/75">
                    Create your business in minutes. No credit card required. Full access to cash up, staff, attendance, inventory and more.
                </p>
            </div>
            <ul class="relative z-10 mt-12 space-y-3 text-sm text-white/80">
                <li class="flex gap-2"><span class="text-primary-300">✓</span> Instant account creation</li>
                <li class="flex gap-2"><span class="text-primary-300">✓</span> Professional plan trial for 14 days</li>
                <li class="flex gap-2"><span class="text-primary-300">✓</span> Main branch created automatically</li>
                <li class="flex gap-2"><span class="text-primary-300">✓</span> Cancel anytime after trial</li>
            </ul>
            <p class="relative z-10 mt-10 text-sm text-white/70">
                Already registered?
                <a href="{{ route('login') }}" class="font-semibold text-white underline decoration-white/40 underline-offset-2 hover:decoration-white">Sign in</a>
            </p>
        </section>

        <section class="flex items-center justify-center px-6 py-12 sm:px-10">
            <div class="w-full max-w-lg">
                <div class="mb-8 lg:hidden">
                    <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center" aria-label="{{ brand_name() }} home">
                        <x-brand-logo height="h-9" class="max-w-none" />
                    </a>
                </div>

                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-primary-600">Get started</p>
                <h2 class="mt-3 font-display text-3xl font-extrabold tracking-tight text-gray-900">Create your business</h2>
                <p class="mt-3 text-sm leading-relaxed text-gray-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-primary-700 hover:text-primary-800">Sign in</a>
                </p>

                <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-4">
                    @csrf
                    <div>
                        <label for="business_name" class="text-sm font-semibold text-gray-900">Business name</label>
                        <input id="business_name" name="business_name" type="text" value="{{ old('business_name') }}" required class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
                        @error('business_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="owner_name" class="text-sm font-semibold text-gray-900">Owner full name</label>
                        <input id="owner_name" name="owner_name" type="text" value="{{ old('owner_name') }}" required class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
                        @error('owner_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="text-sm font-semibold text-gray-900">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
                        @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="text-sm font-semibold text-gray-900">Password</label>
                            <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
                            @error('password') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="text-sm font-semibold text-gray-900">Confirm password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="phone" class="text-sm font-semibold text-gray-900">Business phone <span class="font-normal text-gray-400">(optional)</span></label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
                        </div>
                        <div>
                            <label for="country" class="text-sm font-semibold text-gray-900">Country</label>
                            <select id="country" name="country" required class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
                                @foreach ($countries as $country)
                                    <option value="{{ $country['code'] }}" @selected(old('country', 'GB') === $country['code'])>{{ $country['label'] }}</option>
                                @endforeach
                            </select>
                            @error('country') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label for="business_type" class="text-sm font-semibold text-gray-900">Business type</label>
                        <select id="business_type" name="business_type" required class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20">
                            <option value="">Select type…</option>
                            @foreach ($businessTypes as $type)
                                <option value="{{ $type['value'] }}" @selected(old('business_type') === $type['value'])>{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                        @error('business_type') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <label class="flex items-start gap-3 text-sm text-gray-600">
                        <input type="checkbox" name="terms" value="1" @checked(old('terms')) required class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                        <span>I agree to the <a href="{{ route('terms') }}" class="font-medium text-primary-700 hover:text-primary-800" target="_blank">Terms</a> and <a href="{{ route('privacy') }}" class="font-medium text-primary-700 hover:text-primary-800" target="_blank">Privacy Policy</a>.</span>
                    </label>
                    @error('terms') <p class="text-sm text-danger">{{ $message }}</p> @enderror

                    <button type="submit" class="btn-ripple inline-flex w-full items-center justify-center rounded-2xl bg-primary-600 px-6 py-3.5 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-primary-700">
                        Start My Free Trial
                    </button>
                </form>

                <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-500">
                    <p class="font-semibold text-gray-900">Already have an account?</p>
                    <p class="mt-1">
                        <a href="{{ route('login') }}" class="font-medium text-primary-700 hover:text-primary-800">Sign in to your account</a>
                        — Super Admin, Business Admin and Staff use the same login page.
                    </p>
                </div>

                <p class="mt-10 text-center text-xs text-gray-400">
                    © {{ date('Y') }} TotalCashPro · <a href="{{ route('home') }}" class="hover:text-gray-600">Marketing site</a>
                </p>
            </div>
        </section>
    </div>
</x-layouts.auth>
