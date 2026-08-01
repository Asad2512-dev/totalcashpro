<section id="why" class="scroll-mt-24 py-20 lg:py-28" aria-labelledby="why-heading">
    <x-container>
        <div data-reveal>
            <x-section-title
                eyebrow="Why TotalCashPro"
                title="Simple software. Fair pricing. Real daily use."
                subtitle="Built for owners and managers who want reliable operations tools without another subscription."
            />
        </div>

        <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($whyChoose as $item)
                <article
                    class="rounded-[1.5rem] border border-line bg-gradient-to-b from-snow to-paper p-6 transition duration-300 hover:-translate-y-1 hover:border-royal-600/30 hover:shadow-lift"
                    data-reveal
                >
                    <div class="mb-5 inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-navy-900 text-sky-400">
                        <x-icon :name="$item['icon']" class="h-5 w-5" />
                    </div>
                    <h3 class="font-display text-base font-bold text-navy-900">{{ $item['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-mute">{{ $item['description'] }}</p>
                </article>
            @endforeach
        </div>
    </x-container>
</section>
