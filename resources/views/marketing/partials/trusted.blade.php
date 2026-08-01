<section class="border-y border-line bg-snow py-10" aria-label="Trusted businesses">
    <x-container>
        <div class="flex flex-col items-center gap-6 lg:flex-row lg:gap-10">
            <p class="shrink-0 text-center text-[11px] font-bold uppercase tracking-[0.2em] text-mute lg:w-44 lg:text-left" data-reveal>
                Built for
            </p>

            <div class="relative w-full overflow-hidden" data-reveal>
                <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-12 bg-gradient-to-r from-snow to-transparent"></div>
                <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-12 bg-gradient-to-l from-snow to-transparent"></div>

                <div class="marquee-track flex w-max items-center gap-3">
                    @foreach (array_merge($trustedIndustries, $trustedIndustries) as $industry)
                        <div class="flex h-12 min-w-36 items-center justify-center rounded-full border border-line bg-paper px-5">
                            <span class="font-display text-sm font-bold tracking-tight text-navy-800/80">{{ $industry }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-container>
</section>
