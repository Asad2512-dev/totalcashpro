<x-layouts.marketing :seo="$seo">
    <section class="mesh-hero pt-32 pb-16">
        <x-container class="max-w-3xl">
            <x-badge>About</x-badge>
            <h1 class="mt-5 font-display text-4xl font-extrabold tracking-[-0.03em] text-navy-900 sm:text-5xl">
                Practical software for restaurants and retail
            </h1>
            <p class="mt-6 text-lg leading-relaxed text-mute">
                TotalCashPro is professional operations software for cash up, attendance, inventory, suppliers, payroll, and reports — available as a £29 one-time Lifetime License.
            </p>
        </x-container>
    </section>

    <section class="pb-24">
        <x-container class="max-w-3xl space-y-6">
            <x-card :hover="false">
                <h2 class="font-display text-2xl font-bold text-navy-900">Our focus</h2>
                <p class="mt-3 leading-relaxed text-mute">
                    Business owners should not need five disconnected tools to understand a single trading day. TotalCashPro brings the daily work into one clear system with a fair, one-time price.
                </p>
            </x-card>

            <x-card :hover="false">
                <h2 class="font-display text-2xl font-bold text-navy-900">Pricing philosophy</h2>
                <p class="mt-3 leading-relaxed text-mute">
                    No monthly fees. No yearly fees. No hidden costs. Customers pay £29 once and own the software license.
                </p>
            </x-card>

            <div class="flex flex-col gap-3 sm:flex-row">
                <x-button href="{{ route('home') }}#buy" variant="primary">Buy Now – £29</x-button>
                <x-button href="{{ route('contact') }}" variant="secondary">Contact Us</x-button>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
