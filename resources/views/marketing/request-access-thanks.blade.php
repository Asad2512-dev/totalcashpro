<x-layouts.marketing :seo="$seo">
    <section class="mesh-hero pt-32 pb-24">
        <x-container class="max-w-2xl text-center">
            <x-badge tone="emerald">Request received</x-badge>
            <h1 class="mt-5 font-display text-4xl font-extrabold tracking-[-0.03em] text-navy-900 sm:text-5xl">
                Thank you — we have your request
            </h1>
            <p class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-mute">
                Our team will review your business details and contact you within 24 hours. If approved, we will create your organisation and send login credentials by email.
            </p>
            <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <x-button href="{{ route('home') }}" variant="primary">Back to Home</x-button>
                <x-button href="{{ route('home') }}#pricing" variant="secondary">View Plans</x-button>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
