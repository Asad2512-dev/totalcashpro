<section id="solutions" class="scroll-mt-24 bg-paper py-20 lg:py-28" aria-labelledby="industries-heading">
    <x-container>
        <div class="grid gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:gap-16">
            <div class="lg:sticky lg:top-28 lg:self-start" data-reveal>
                <x-section-title
                    align="left"
                    eyebrow="Industries"
                    title="Made for restaurants and retail teams."
                    subtitle="Whether you serve tables, pour coffee, or run a shop — TotalCashPro covers the daily work that keeps the business moving."
                />
                <div class="mt-8">
                    <x-button href="{{ route('register') }}" variant="primary">Start Free Trial</x-button>
                </div>
            </div>

            <div class="divide-y divide-line rounded-[1.75rem] border border-line bg-snow">
                @foreach ($industries as $industry)
                    <article
                        class="group grid gap-2 px-6 py-6 transition duration-300 hover:bg-navy-50/60 sm:grid-cols-[12rem_1fr] sm:items-center sm:gap-8 sm:px-8"
                        data-reveal
                    >
                        <div class="flex items-center gap-3">
                            <span @class([
                                'h-2 w-2 rounded-full',
                                'bg-royal-600' => $industry['accent'] === 'royal',
                                'bg-emerald-500' => $industry['accent'] === 'emerald',
                                'bg-sky-400' => $industry['accent'] === 'sky',
                            ])></span>
                            <h3 class="font-display text-lg font-bold tracking-tight text-navy-900">
                                {{ $industry['title'] }}
                            </h3>
                        </div>
                        <p class="text-sm leading-relaxed text-mute sm:text-base">
                            {{ $industry['description'] }}
                        </p>
                    </article>
                @endforeach
            </div>
        </div>
    </x-container>
</section>
