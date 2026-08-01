<x-layouts.marketing :seo="$seo">
    <section class="pt-32 pb-24">
        <x-container width="narrow">
            <x-badge tone="navy">Legal</x-badge>
            <h1 class="mt-5 font-display text-4xl font-extrabold tracking-tight text-navy-900">Privacy Policy</h1>
            <p class="mt-4 text-sm text-mute">Last updated: August 1, 2026</p>

            <div class="mt-10 space-y-8 leading-relaxed text-mute">
                <div>
                    <h2 class="font-display text-xl font-bold text-navy-900">Overview</h2>
                    <p class="mt-3">
                        This privacy policy explains how {{ brand_name() }} collects and uses information through the marketing website at totalcashpro.com. Customer account and license data processing will be documented separately when purchase and product access go live.
                    </p>
                </div>
                <div>
                    <h2 class="font-display text-xl font-bold text-navy-900">Information we collect</h2>
                    <p class="mt-3">
                        When you contact us, we may collect your name, email address, business details, and the contents of your message. Standard technical logs such as IP address, browser type, and pages visited may also be processed to operate and secure the website.
                    </p>
                </div>
                <div>
                    <h2 class="font-display text-xl font-bold text-navy-900">How we use information</h2>
                    <p class="mt-3">
                        We use contact details to respond to enquiries, support license questions, improve the website experience, and maintain security. We do not sell personal information.
                    </p>
                </div>
                <div>
                    <h2 class="font-display text-xl font-bold text-navy-900">Contact</h2>
                    <p class="mt-3">
                        Privacy questions can be sent to
                        <a class="font-medium text-royal-600 hover:text-royal-700" href="mailto:{{ config('totalcashpro.support_email') }}">{{ config('totalcashpro.support_email') }}</a>.
                    </p>
                </div>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
