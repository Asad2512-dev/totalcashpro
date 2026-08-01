<section id="testimonials" class="scroll-mt-24 py-20 lg:py-28" aria-labelledby="testimonials-heading">
    <x-container>
        <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-end">
            <div data-reveal>
                <x-section-title
                    align="left"
                    eyebrow="Testimonials"
                    title="What business owners care about."
                    subtitle="Placeholder stories based on the practical needs of restaurant and retail teams."
                />
            </div>
            <div class="h-px w-full hairline lg:mb-3" data-reveal></div>
        </div>

        <div class="mt-12 grid gap-5 lg:grid-cols-3">
            @foreach ($testimonials as $testimonial)
                <x-testimonial
                    :quote="$testimonial['quote']"
                    :name="$testimonial['name']"
                    :role="$testimonial['role']"
                    :business="$testimonial['business']"
                />
            @endforeach
        </div>
    </x-container>
</section>
