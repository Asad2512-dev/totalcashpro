<section id="features" class="scroll-mt-24 py-20 lg:py-28" aria-labelledby="features-heading">
    <x-container>
        <div class="grid gap-10 lg:grid-cols-[0.85fr_1.15fr] lg:items-end">
            <div data-reveal>
                <x-section-title
                    align="left"
                    eyebrow="Features"
                    title="Everything you need — clearly labelled by plan."
                    subtitle="Basic covers cash, staff and daily reports. Professional adds inventory, payroll, unlimited staff and more."
                />
            </div>
            <p class="max-w-xl text-sm leading-relaxed text-mute lg:justify-self-end lg:text-right" data-reveal>
                Each feature shows whether it is included in Basic or available on Professional only — so you know exactly what you are requesting.
            </p>
        </div>

        <div class="mt-14 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
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
    </x-container>
</section>
