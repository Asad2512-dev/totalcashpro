<section id="pricing" class="scroll-mt-24 pricing-spotlight py-20 lg:py-28" aria-labelledby="pricing-heading">
    <x-container>
        <div class="mx-auto max-w-2xl text-center" data-reveal>
            <span class="inline-flex items-center rounded-full bg-[#EFF6FF] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-[#1E40AF] ring-1 ring-inset ring-[#2563EB]/15">
                Pricing
            </span>
            <h2 id="pricing-heading" class="mt-4 font-display text-3xl font-extrabold tracking-[-0.03em] text-[#111827] sm:text-4xl lg:text-[2.75rem]">
                Choose your plan
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-base leading-relaxed text-[#6B7280] sm:text-lg">
                Monthly cloud subscriptions for restaurants and retail. Request access — accounts are reviewed and created by our team.
            </p>
        </div>

        <div id="buy" class="scroll-mt-28 mx-auto mt-16 grid max-w-5xl items-stretch gap-6 pt-4 lg:grid-cols-2 lg:gap-8 lg:items-center">
            @foreach ($pricingPlans as $plan)
                <x-pricing-card
                    :name="$plan['name']"
                    :price="$plan['price']"
                    :period="$plan['period']"
                    :badge="$plan['badge']"
                    :popular-badge="$plan['popularBadge']"
                    :description="$plan['description']"
                    :features="$plan['features']"
                    :cta="$plan['cta']"
                    :assurances="$plan['assurances']"
                    :featured="$plan['featured']"
                    :cta-href="$plan['ctaHref']"
                />
            @endforeach
        </div>

        <p class="mx-auto mt-10 max-w-2xl text-center text-sm leading-relaxed text-[#6B7280]" data-reveal>
            There is no free trial and no instant signup. Submit a request, and we will review it before creating your account.
        </p>
    </x-container>
</section>
