<x-layouts.marketing :seo="$seo">
    <section class="mesh-hero pt-32 pb-16">
        <x-container class="max-w-3xl">
            <x-badge>About</x-badge>
            <h1 class="mt-5 font-display text-4xl font-extrabold tracking-[-0.03em] text-navy-900 sm:text-5xl">
                Practical cloud software for restaurants and retail
            </h1>
            <p class="mt-6 text-lg leading-relaxed text-mute">
                TotalCashPro is a cloud SaaS platform for daily cash, staff, inventory and reports — with Basic at £19.99/month and Professional at £29.99/month.
            </p>
        </x-container>
    </section>

    <section class="pb-24">
        <x-container class="max-w-3xl space-y-6">
            <x-card :hover="false">
                <h2 class="font-display text-2xl font-bold text-navy-900">Our focus</h2>
                <p class="mt-3 leading-relaxed text-mute">
                    Business owners should not need five disconnected tools or endless spreadsheets to understand a single trading day. TotalCashPro brings the daily work into one secure cloud dashboard.
                </p>
            </x-card>

            <x-card :hover="false">
                <h2 class="font-display text-2xl font-bold text-navy-900">How access works</h2>
                <p class="mt-3 leading-relaxed text-mute">
                    There is no instant registration and no free trial. You request access, our team reviews your business, creates your organisation and admin account, then emails your login credentials.
                </p>
            </x-card>

            <div class="flex flex-col gap-3 sm:flex-row">
                <x-button href="{{ route('request-access') }}" variant="primary">Request Access</x-button>
                <x-button href="{{ route('home') }}#pricing" variant="secondary">Choose Your Plan</x-button>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
