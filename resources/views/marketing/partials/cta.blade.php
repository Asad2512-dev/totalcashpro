<section id="get-started" class="scroll-mt-24 bg-gray-50 py-20 lg:py-28" aria-labelledby="cta-heading">
    <x-container>
        <div class="relative overflow-hidden rounded-[2rem] bg-gray-900 px-8 py-16 text-white sm:px-12 lg:px-16" data-reveal>
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -left-16 top-0 h-64 w-64 rounded-full bg-primary-600/30 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 h-72 w-72 rounded-full bg-primary-400/20 blur-3xl"></div>
            </div>

            <div class="relative mx-auto max-w-3xl text-center">
                <p class="font-display text-[11px] font-bold uppercase tracking-[0.22em] text-primary-300">
                    Request a demo
                </p>
                <h2 id="cta-heading" class="mt-4 font-display text-3xl font-extrabold tracking-[-0.03em] text-white sm:text-4xl lg:text-5xl">
                    Ready to see TotalCashPro?
                </h2>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-white/75 sm:text-lg">
                    Choose Basic or Professional, submit your business request, and our team will review it before creating your account.
                </p>
                <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="btn-ripple inline-flex items-center justify-center rounded-2xl bg-primary-600 px-6 py-3.5 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-primary-700">
                        Start Free Trial
                    </a>
                    <a href="#pricing" class="inline-flex items-center justify-center rounded-2xl border border-white/40 bg-transparent px-6 py-3.5 text-base font-semibold text-white transition hover:bg-white/10">
                        Choose Your Plan
                    </a>
                </div>
            </div>
        </div>
    </x-container>
</section>
