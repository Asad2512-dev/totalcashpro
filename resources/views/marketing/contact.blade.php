<x-layouts.marketing :seo="$seo">
    <section class="mesh-hero pt-32 pb-16">
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
            <div class="grid gap-6 lg:grid-cols-2">
                <x-card :hover="false">
                    <h2 class="font-display text-2xl font-bold text-navy-900">Get in touch</h2>
                    <p class="mt-3 leading-relaxed text-mute">
                        Prefer email first? Contact us and we will help you decide which plan to request.
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
                        <x-button href="{{ route('request-access') }}" variant="secondary">Request Access</x-button>
                    </div>
                </x-card>

                <x-card :hover="false" class="bg-navy-900 text-white">
                    <h2 class="font-display text-2xl font-bold">What we can help with</h2>
                    <ul class="mt-6 space-y-4 text-sm text-white/70">
                        <li class="flex gap-3">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-sky-400"></span>
                            Choosing between Basic (£19.99/month) and Professional (£29.99/month).
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-sky-400"></span>
                            Explaining how the request and account setup process works.
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-sky-400"></span>
                            Support questions after your account has been created.
                        </li>
                    </ul>
                </x-card>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
