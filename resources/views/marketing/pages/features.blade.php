<x-layouts.marketing :seo="$seo">
    <section class="mesh-hero pt-32 pb-12">
        <x-container class="max-w-3xl">
            <x-badge>Features</x-badge>
            <h1 class="mt-5 font-display text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                Tools for the trading day
            </h1>
            <p class="mt-6 text-lg text-mute">
                Every feature is labelled by plan so you know exactly what Basic and Professional include.
            </p>
        </x-container>
    </section>
    <section class="pb-24">
        <x-container>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($features as $feature)
                    <x-feature-card
                        :title="$feature['title']"
                        :description="$feature['description']"
                        :icon="$feature['icon']"
                        :plan="$feature['plan']"
                        :plan-label="$feature['planLabel']"
                    />
                @endforeach
            </div>
            <div class="mt-12 flex flex-col gap-3 sm:flex-row">
                <x-button href="{{ route('request-demo') }}" variant="primary">Request Demo</x-button>
                <x-button href="{{ route('pricing') }}" variant="secondary">View Pricing</x-button>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
