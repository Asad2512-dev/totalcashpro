<div
    x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }"
    class="border-b border-line"
    data-reveal
>
    <button
        type="button"
        class="flex w-full items-center justify-between gap-6 py-5 text-left"
        @click="open = !open"
        :aria-expanded="open.toString()"
    >
        <span class="font-display text-base font-bold tracking-tight text-navy-900 sm:text-lg">
            {{ $question }}
        </span>
        <span
            class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-line text-navy-800 transition duration-300"
            :class="open ? 'rotate-45 border-royal-600/30 bg-royal-50 text-royal-600' : 'bg-snow'"
            aria-hidden="true"
        >
            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none">
                <path d="M8 3.5V12.5M3.5 8H12.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
        </span>
    </button>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="pb-5 pr-12"
    >
        <p class="text-sm leading-relaxed text-mute sm:text-base">
            {{ $answer }}
        </p>
    </div>
</div>
