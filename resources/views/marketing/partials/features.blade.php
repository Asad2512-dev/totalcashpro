<section id="features" class="scroll-mt-24 py-20 lg:py-28" aria-labelledby="features-heading">
    <x-container>
        <div class="grid gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:items-end">
            <div data-reveal>
                <x-section-title
                    align="left"
                    eyebrow="Features"
                    title="Everything you need to run the day."
                    subtitle="Cash up, staff time, inventory, suppliers, payroll, and reports — included in one Lifetime License."
                />
            </div>
            <p class="max-w-xl text-sm leading-relaxed text-mute lg:justify-self-end lg:text-right" data-reveal>
                Clear tools for restaurant and retail owners who want practical software without monthly fees.
            </p>
        </div>

        <div class="mt-14 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($features as $feature)
                <x-feature-card
                    :title="$feature['title']"
                    :description="$feature['description']"
                    :icon="$feature['icon']"
                />
            @endforeach
        </div>
    </x-container>
</section>
