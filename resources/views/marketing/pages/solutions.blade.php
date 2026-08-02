<x-layouts.marketing :seo="$seo">
    <section class="mesh-hero pt-32 pb-12">
        <x-container class="max-w-3xl">
            <x-badge>Solutions</x-badge>
            <h1 class="mt-5 font-display text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                Built for restaurants and retail teams
            </h1>
            <p class="mt-6 text-lg text-mute">
                Practical cloud software for the businesses that run on cash, shifts and stock.
            </p>
        </x-container>
    </section>
    <section class="pb-24">
        <x-container>
            <div class="divide-y divide-line rounded-[1.75rem] border border-line bg-snow">
                @foreach ($industries as $industry)
                    <article class="grid gap-2 px-6 py-6 sm:grid-cols-[12rem_1fr] sm:items-center sm:gap-8 sm:px-8">
                        <h2 class="font-display text-lg font-bold text-gray-900">{{ $industry['title'] }}</h2>
                        <p class="text-sm leading-relaxed text-mute sm:text-base">{{ $industry['description'] }}</p>
                    </article>
                @endforeach
            </div>
            <div class="mt-12">
                <x-button href="{{ route('register') }}" variant="primary">Get Started</x-button>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
