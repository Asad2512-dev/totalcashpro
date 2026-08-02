<x-layouts.marketing :seo="$seo">
    <section class="mesh-hero pt-32 pb-12">
        <x-container class="max-w-3xl">
            <x-badge>Contact</x-badge>
            <h1 class="mt-5 font-display text-4xl font-extrabold tracking-[-0.03em] text-navy-900 sm:text-5xl">
                Questions before you request access?
            </h1>
            <p class="mt-6 text-lg leading-relaxed text-mute">
                Ask about Basic or Professional, the request process, or whether TotalCashPro fits your restaurant or retail business.
            </p>
        </x-container>
    </section>

    <section class="pb-24">
        <x-container>
            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr] lg:gap-8">
                <div class="space-y-6">
                    <article class="rounded-[1.5rem] border border-line bg-snow p-6 shadow-[0_1px_0_rgb(15_23_42_/_0.03)] md:p-7">
                        <h2 class="font-display text-2xl font-bold text-navy-900">Get in touch</h2>
                        <p class="mt-3 leading-relaxed text-mute">
                            Prefer email first? Send a message using the form, or email us directly.
                        </p>
                        <div class="mt-8 space-y-4 text-sm">
                            <div>
                                <p class="font-semibold text-navy-900">Email</p>
                                <a class="text-royal-600 transition hover:text-royal-700" href="mailto:{{ config('totalcashpro.support_email') }}">
                                    {{ config('totalcashpro.support_email') }}
                                </a>
                            </div>
                            <div>
                                <p class="font-semibold text-navy-900">Subscription prices</p>
                                <p class="text-mute">Basic £19.99/month · Professional £29.99/month</p>
                            </div>
                        </div>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <x-button href="mailto:{{ config('totalcashpro.support_email') }}" variant="primary">Email TotalCashPro</x-button>
                            <x-button href="{{ route('register') }}" variant="secondary">Start Free Trial</x-button>
                        </div>
                    </article>

                    <article class="rounded-[1.5rem] border border-[#1E293B] bg-[#0F172A] p-6 text-white md:p-7">
                        <h2 class="font-display text-2xl font-bold text-white">What we can help with</h2>
                        <ul class="mt-6 space-y-4 text-sm text-white/75">
                            <li class="flex gap-3">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-[#38BDF8]"></span>
                                <span>Choosing between Basic (£19.99/month) and Professional (£29.99/month).</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-[#38BDF8]"></span>
                                <span>Explaining how the request and account setup process works.</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-[#38BDF8]"></span>
                                <span>Support questions after your account has been created.</span>
                            </li>
                        </ul>
                    </article>
                </div>

                <article class="rounded-[1.5rem] border border-line bg-snow p-6 shadow-soft md:p-8">
                    <h2 class="font-display text-2xl font-bold text-navy-900">Send a message</h2>
                    <p class="mt-2 text-sm leading-relaxed text-mute">
                        Fill in the form and our team will get back to you within 24 hours.
                    </p>

                    <form method="POST" action="{{ route('contact.store') }}" class="mt-8 space-y-5">
                        @csrf

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="name" class="text-sm font-semibold text-navy-900">Your Name</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name') }}"
                                    required
                                    class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20"
                                >
                                @error('name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="email" class="text-sm font-semibold text-navy-900">Email Address</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20"
                                >
                                @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="phone" class="text-sm font-semibold text-navy-900">Phone <span class="font-normal text-mute">(optional)</span></label>
                                <input
                                    id="phone"
                                    name="phone"
                                    type="text"
                                    value="{{ old('phone') }}"
                                    class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20"
                                >
                                @error('phone') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="subject" class="text-sm font-semibold text-navy-900">Subject</label>
                                <input
                                    id="subject"
                                    name="subject"
                                    type="text"
                                    value="{{ old('subject') }}"
                                    required
                                    class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20"
                                >
                                @error('subject') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="message" class="text-sm font-semibold text-navy-900">Message</label>
                            <textarea
                                id="message"
                                name="message"
                                rows="6"
                                required
                                class="mt-2 w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm text-ink outline-none transition focus:border-royal-600 focus:ring-2 focus:ring-royal-600/20"
                            >{{ old('message') }}</textarea>
                            @error('message') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>

                        <button
                            type="submit"
                            class="btn-ripple inline-flex w-full items-center justify-center rounded-2xl bg-royal-600 px-6 py-3.5 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-royal-800 sm:w-auto"
                        >
                            Send Message
                        </button>
                    </form>
                </article>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
