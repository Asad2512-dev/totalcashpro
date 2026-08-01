<x-layouts.marketing :seo="$seo">
    <section class="pt-32 pb-24">
        <x-container width="narrow">
            <x-badge tone="navy">Legal</x-badge>
            <h1 class="mt-5 font-display text-4xl font-extrabold tracking-tight text-navy-900">Terms of Service</h1>
            <p class="mt-4 text-sm text-mute">Last updated: August 1, 2026</p>

            <div class="mt-10 space-y-8 leading-relaxed text-mute">
                <div>
                    <h2 class="font-display text-xl font-bold text-navy-900">Acceptance</h2>
                    <p class="mt-3">
                        By accessing the {{ brand_name() }} website, you agree to these terms. If you do not agree, please discontinue use of the site.
                    </p>
                </div>
                <div>
                    <h2 class="font-display text-xl font-bold text-navy-900">License model</h2>
                    <p class="mt-3">
                        TotalCashPro is offered as a one-time Lifetime License for £29. There are no monthly or yearly subscription fees described on this marketing website. Final purchase terms will apply at checkout when payment processing is enabled.
                    </p>
                </div>
                <div>
                    <h2 class="font-display text-xl font-bold text-navy-900">Website purpose</h2>
                    <p class="mt-3">
                        This website currently provides marketing information about the TotalCashPro product. Features and availability may evolve as development continues.
                    </p>
                </div>
                <div>
                    <h2 class="font-display text-xl font-bold text-navy-900">Contact</h2>
                    <p class="mt-3">
                        Questions about these terms can be sent to
                        <a class="font-medium text-royal-600 hover:text-royal-700" href="mailto:{{ config('totalcashpro.support_email') }}">{{ config('totalcashpro.support_email') }}</a>.
                    </p>
                </div>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
