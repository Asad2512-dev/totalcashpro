<x-layouts.marketing :seo="$seo">
    <section class="mesh-hero pt-32 pb-12">
        <x-container class="max-w-3xl">
            <x-badge tone="emerald">Features</x-badge>
            <h1 class="mt-5 font-display text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                Everything in TotalCashPro v1.0
            </h1>
            <p class="mt-6 text-lg text-mute">
                Attendance kiosk, finance, payroll, inventory, CRM, HR, reports and security — grouped so you can see exactly what ships today.
            </p>
        </x-container>
    </section>

    @foreach ($featureCategories as $category)
        <section class="py-16 {{ $loop->even ? 'bg-paper' : '' }}" aria-labelledby="cat-{{ $loop->index }}">
            <x-container>
                <div class="max-w-2xl">
                    <h2 id="cat-{{ $loop->index }}" class="font-display text-2xl font-extrabold text-navy-900 sm:text-3xl">{{ $category['title'] }}</h2>
                    <p class="mt-3 text-mute">{{ $category['description'] }}</p>
                </div>
                <ul class="mt-10 grid gap-4 sm:grid-cols-2">
                    @foreach ($category['items'] as $item)
                        <li class="rounded-2xl border border-line bg-snow p-5 shadow-sm">
                            <h3 class="font-display font-bold text-navy-900">{{ $item['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-mute">{{ $item['description'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </x-container>
        </section>
    @endforeach

    <section class="pb-24 pt-8">
        <x-container>
            <h2 class="font-display text-2xl font-extrabold text-navy-900">All features by plan</h2>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
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
                <x-button href="{{ route('register') }}" variant="primary">Start Free Trial</x-button>
                <x-button href="{{ route('pricing') }}" variant="secondary">View Pricing</x-button>
            </div>
        </x-container>
    </section>
</x-layouts.marketing>
