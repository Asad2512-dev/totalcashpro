<x-layouts.marketing :seo="$seo">
    @include('marketing.partials.pricing')
    <section class="pb-24">
        <x-container class="text-center">
            <x-button href="{{ route('request-demo') }}" variant="primary" size="lg">Request Demo</x-button>
        </x-container>
    </section>
</x-layouts.marketing>
