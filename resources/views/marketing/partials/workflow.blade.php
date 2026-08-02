<section id="workflow" class="scroll-mt-24 py-20 lg:py-28" aria-labelledby="workflow-heading">
    <x-container>
        <div data-reveal>
            <x-section-title
                eyebrow="How It Works"
                title="From signup to your first dashboard."
                subtitle="Create your account, verify email, and start managing your business in minutes."
            />
        </div>

        <div class="relative mt-16">
            <div class="pointer-events-none absolute left-[1.15rem] top-3 bottom-3 w-px bg-gradient-to-b from-royal-600 via-sky-400 to-navy-200 md:left-0 md:right-0 md:top-8 md:h-px md:w-auto md:bg-gradient-to-r" aria-hidden="true"></div>

            <ol class="grid gap-8 md:grid-cols-3 xl:grid-cols-5 md:gap-5">
                @foreach ($workflow as $item)
                    <li class="relative pl-12 md:pl-0 md:pt-10" data-reveal>
                        <span class="absolute left-0 top-0 flex h-9 w-9 items-center justify-center rounded-full border border-line bg-snow font-display text-xs font-extrabold text-navy-900 shadow-sm md:left-1/2 md:top-0 md:-translate-x-1/2">
                            {{ $item['step'] }}
                        </span>
                        <h3 class="font-display text-base font-bold tracking-tight text-navy-900 md:text-center">
                            {{ $item['title'] }}
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed text-mute md:text-center">
                            {{ $item['description'] }}
                        </p>
                    </li>
                @endforeach
            </ol>
        </div>
    </x-container>
</section>
