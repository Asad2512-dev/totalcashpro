<div
    id="license-card"
    data-reveal
    class="relative mx-auto w-full max-w-lg overflow-hidden rounded-3xl border border-[#E5E7EB] bg-white shadow-[0_24px_60px_-36px_rgba(15,23,42,0.35)]"
>
    <div class="bg-[#0F172A] px-8 py-8 text-center text-white sm:px-10">
        <span class="inline-flex items-center rounded-full bg-[#10B981]/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#6EE7B7] ring-1 ring-inset ring-[#10B981]/30">
            {{ $badge }}
        </span>
        <h3 class="mt-4 font-display text-2xl font-bold tracking-tight text-white">{{ $name }}</h3>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-white/70">
            {{ $description }}
        </p>
        <div class="mt-6 flex items-end justify-center gap-1">
            <span class="font-display text-6xl font-extrabold tracking-tight text-white sm:text-7xl">{{ $price }}</span>
        </div>
        <p class="mt-2 text-sm font-medium text-[#38BDF8]">Pay once. Own it forever.</p>
    </div>

    <div class="px-8 py-8 sm:px-10">
        <ul class="space-y-3">
            @foreach ($features as $feature)
                <li class="flex items-start gap-3 text-sm text-[#111827]">
                    <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#ECFDF5] text-[#059669]">
                        <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M2.5 6.2L4.8 8.5L9.5 3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>{{ $feature }}</span>
                </li>
            @endforeach
        </ul>

        <div class="mt-8 border-t border-[#E5E7EB] pt-8 text-center">
            <a
                href="{{ $ctaHref }}"
                class="btn-ripple inline-flex w-full items-center justify-center rounded-2xl bg-[#2563EB] px-6 py-3.5 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-[#1D4ED8]"
            >
                {{ $cta }}
            </a>
            <p class="mt-4 text-sm font-medium text-[#6B7280]">{{ $note }}</p>
        </div>
    </div>
</div>
