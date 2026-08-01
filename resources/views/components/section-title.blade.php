<div {{ $attributes->class(['flex max-w-3xl flex-col gap-4', $alignClasses()]) }}>
    @if ($eyebrow)
        <x-badge>{{ $eyebrow }}</x-badge>
    @endif

    <h2 class="font-display text-3xl font-bold tracking-[-0.03em] text-navy-900 sm:text-4xl lg:text-[2.75rem] lg:leading-[1.12]">
        {{ $title }}
    </h2>

    @if ($subtitle)
        <p class="max-w-2xl text-base leading-relaxed text-mute sm:text-lg">
            {{ $subtitle }}
        </p>
    @endif
</div>
