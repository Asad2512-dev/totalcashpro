<section id="faq" class="scroll-mt-24 bg-paper py-20 lg:py-28" aria-labelledby="faq-heading">
    <x-container>
        <div class="grid gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:items-start lg:gap-16">
            <div data-reveal>
                <x-section-title
                    align="left"
                    eyebrow="FAQ"
                    title="Clear answers about price and product."
                    subtitle="The questions business owners usually ask before buying a Lifetime License."
                />
            </div>

            <div class="rounded-[1.75rem] border border-line bg-snow px-6 sm:px-8">
                @foreach ($faqs as $index => $faq)
                    <x-faq-item
                        :question="$faq['question']"
                        :answer="$faq['answer']"
                        :index="$index"
                    />
                @endforeach
            </div>
        </div>
    </x-container>
</section>
