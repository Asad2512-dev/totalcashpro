<figure class="flex h-full flex-col rounded-[1.5rem] border border-line bg-snow p-7" data-reveal>
    <svg class="h-6 w-6 text-royal-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M7.2 6.4C4.9 8.1 3.5 10.4 3.5 13.2c0 2.4 1.5 4 3.5 4 1.8 0 3.2-1.4 3.2-3.2S8.9 10.7 7.2 10.5c.2-1.4 1.3-2.8 3.2-4.1L9 5.2c-1 .5-1.8 1.1-2.8 1.2zm10.1 0c-2.3 1.7-3.7 4-3.7 6.8 0 2.4 1.5 4 3.5 4 1.8 0 3.2-1.4 3.2-3.2s-1.3-3.3-3-3.5c.2-1.4 1.3-2.8 3.2-4.1L18.1 5.2c-1 .5-1.8 1.1-2.8 1.2z"/>
    </svg>

    <blockquote class="mt-5 flex-1 text-base leading-relaxed text-navy-800">
        “{{ $quote }}”
    </blockquote>

    <figcaption class="mt-8 flex items-center gap-3 border-t border-line pt-5">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-navy-900 font-display text-sm font-bold text-white">
            {{ $initials() }}
        </div>
        <div>
            <p class="font-semibold text-navy-900">{{ $name }}</p>
            <p class="text-sm text-mute">{{ $role }}, {{ $business }}</p>
        </div>
    </figcaption>
</figure>
