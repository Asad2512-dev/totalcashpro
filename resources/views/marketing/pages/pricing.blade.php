<x-layouts.marketing :seo="$seo">
    @include('marketing.partials.pricing')
    <section class="pb-24">
        <x-container class="text-center">
            <x-button href="{{ route('register') }}" variant="primary" size="lg">Start Free Trial</x-button>
        </x-container>
    </section>
</x-layouts.marketing>
