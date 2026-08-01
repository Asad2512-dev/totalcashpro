<section id="pricing" class="scroll-mt-24 bg-[#F8FAFC] py-20 lg:py-28" aria-labelledby="pricing-heading">
    <x-container>
        <div class="mx-auto max-w-2xl text-center" data-reveal>
            <span class="inline-flex items-center rounded-full bg-[#EFF6FF] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-[#1D4ED8] ring-1 ring-inset ring-[#2563EB]/15">
                Pricing
            </span>
            <h2 id="pricing-heading" class="mt-4 font-display text-3xl font-extrabold tracking-[-0.03em] text-[#0F172A] sm:text-4xl lg:text-[2.75rem]">
                One simple price.
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-[#6B7280] sm:text-lg">
                Buy once for £29. No subscriptions. No recurring charges. No hidden costs.
            </p>
        </div>

        <div id="buy" class="scroll-mt-28 mt-12">
            <x-pricing-card
                :name="$pricingPlan['name']"
                :price="$pricingPlan['price']"
                :badge="$pricingPlan['badge']"
                :description="$pricingPlan['description']"
                :features="$pricingPlan['features']"
                :cta="$pricingPlan['cta']"
                :note="$pricingPlan['note']"
                cta-href="#buy"
            />
        </div>
    </x-container>
</section>
