<x-layouts.marketing :seo="$seo">
    <section class="mesh-hero pt-32 pb-16">
        <x-container class="max-w-3xl">
            <x-badge>Contact</x-badge>
            <h1 class="mt-5 font-display text-4xl font-extrabold tracking-[-0.03em] text-navy-900 sm:text-5xl">
                Questions before you buy?
            </h1>
            <p class="mt-6 text-lg leading-relaxed text-mute">
                Ask about the Lifetime License, features, or whether TotalCashPro fits your restaurant or retail business.
            </p>
        </x-container>
    </section>

    <section class="pb-24">
        <x-container>
            <div class="grid gap-6 lg:grid-cols-2">
                <x-card :hover="false">
                    <h2 class="font-display text-2xl font-bold text-navy-900">Get in touch</h2>
                    <p class="mt-3 leading-relaxed text-mute">
                        Checkout will be connected in a later phase. For now, email us and we will help you with license questions.
                    </p>
                    <div class="mt-8 space-y-4 text-sm">
                        <div>
                            <p class="font-semibold text-navy-900">Email</p>
                            <a class="text-royal-600 transition hover:text-royal-700" href="mailto:{{ config('totalcashpro.support_email') }}">
                                {{ config('totalcashpro.support_email') }}
                            </a>
                        </div>
                        <div>
                            <p class="font-semibold text-navy-900">License price</p>
                            <p class="text-mute">£29 one-time · No subscriptions</p>
                        </div>
                    </div>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <x-button href="mailto:{{ config('totalcashpro.support_email') }}" variant="primary">Email TotalCashPro</x-button>
                        <x-button href="{{ route('home') }}#buy" variant="secondary">View Pricing</x-button>
                    </div>
                </x-card>

                <x-card :hover="false" class="bg-navy-900 text-white">
                    <h2 class="font-display text-2xl font-bold">What we can help with</h2>
                    <ul class="mt-6 space-y-4 text-sm text-white/70">
                        <li class="flex gap-3">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-sky-400"></span>
                            Confirming what is included in the £29 Lifetime License.
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-sky-400"></span>
                            Understanding whether TotalCashPro fits your daily operations.
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-sky-400"></span>
                            Support questions after you purchase.
                        </li>
                    </ul>
                </x-card>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
