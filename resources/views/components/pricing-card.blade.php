@props([
    'name',
    'price',
    'badge',
    'description',
    'features',
    'cta',
    'assurances' => [],
    'period' => '/month',
    'popularBadge' => null,
    'featured' => false,
    'ctaHref' => '#',
])

<article
    @class([
        'pricing-card relative flex h-full flex-col overflow-visible rounded-[1.75rem] border bg-white transition duration-300',
        'border-line shadow-[0_18px_40px_-28px_rgba(15,23,42,0.18)] hover:-translate-y-1 hover:shadow-[0_24px_50px_-30px_rgba(15,23,42,0.28)]' => ! $featured,
        'pricing-card--featured z-10 scale-[1.02] border-[#16A34A]/40 shadow-[0_28px_70px_-32px_rgba(22,163,74,0.45)] ring-1 ring-[#16A34A]/20 hover:-translate-y-1.5 hover:shadow-[0_36px_80px_-30px_rgba(22,163,74,0.55)] lg:scale-105' => $featured,
    ])
    data-reveal
>
    @if ($featured)
        <div class="pointer-events-none absolute inset-0 overflow-hidden rounded-[1.75rem] pricing-card-glow" aria-hidden="true"></div>
    @endif

    @if ($popularBadge)
        <div class="absolute inset-x-0 top-0 z-20 flex -translate-y-1/2 justify-center px-4">
            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full bg-[#15803D] px-3.5 py-1.5 text-[11px] font-bold uppercase tracking-[0.14em] text-white shadow-md">
                <span aria-hidden="true">★</span>
                {{ $popularBadge }}
            </span>
        </div>
    @endif

    <div @class([
        'relative px-7 pb-6 pt-8 sm:px-8',
        'pt-11' => (bool) $popularBadge,
    ])>
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center rounded-full bg-[#F0FDF4] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.12em] text-[#15803D] ring-1 ring-inset ring-[#16A34A]/15">
                {{ $badge }}
            </span>
        </div>

        <h3 class="mt-4 font-display text-2xl font-bold tracking-tight text-[#111827]">{{ $name }}</h3>
        <p class="mt-2 text-sm leading-relaxed text-[#6B7280]">{{ $description }}</p>

        <div class="mt-6 flex items-end gap-1.5">
            <span class="font-display text-5xl font-extrabold tracking-tight text-[#111827] sm:text-[3.25rem]">{{ $price }}</span>
            <span class="mb-2 text-base font-semibold text-[#6B7280]">{{ $period }}</span>
        </div>
        <p class="mt-2 text-sm font-medium text-[#6B7280]">Cloud SaaS · 14-day free trial included</p>
    </div>

    <div class="relative flex flex-1 flex-col border-t border-[#E5E7EB] px-7 py-7 sm:px-8">
        <ul class="flex-1 space-y-2.5">
            @foreach ($features as $feature)
                <li class="flex items-start gap-3 text-sm text-[#111827]">
                    <span @class([
                        'mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full',
                        'bg-[#ECFDF5] text-[#059669]' => ! $featured,
                        'bg-[#F0FDF4] text-[#16A34A]' => $featured,
                    ])>
                        <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M2.5 6.2L4.8 8.5L9.5 3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>{{ $feature }}</span>
                </li>
            @endforeach
        </ul>

        <div class="mt-8">
            <a
                href="{{ $ctaHref }}"
                @class([
                    'btn-ripple inline-flex w-full items-center justify-center rounded-2xl px-6 py-3.5 text-base font-semibold transition',
                    'border border-[#E5E7EB] bg-white text-[#111827] hover:-translate-y-0.5 hover:border-[#16A34A]/40 hover:text-[#15803D]' => ! $featured,
                    'bg-[#16A34A] text-white hover:-translate-y-0.5 hover:bg-[#15803D]' => $featured,
                ])
            >
                {{ $cta }}
            </a>

            @if (count($assurances) > 0)
                <ul class="mt-5 space-y-2">
                    @foreach ($assurances as $assurance)
                        <li class="flex items-center justify-center gap-2 text-sm font-medium text-[#6B7280]">
                            <svg class="h-3.5 w-3.5 shrink-0 text-[#10B981]" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <path d="M2.5 6.2L4.8 8.5L9.5 3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ $assurance }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</article>
