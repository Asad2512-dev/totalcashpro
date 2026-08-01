<article
    {{ $attributes->class('group relative overflow-hidden rounded-[1.5rem] border border-line bg-snow p-6 transition duration-300 hover:-translate-y-1 hover:border-royal-600/25 hover:shadow-lift md:p-7') }}
    data-reveal
>
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-royal-600/50 to-transparent opacity-0 transition duration-300 group-hover:opacity-100"></div>

    <div class="mb-5 flex items-start justify-between gap-3">
        <div class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-navy-900 text-sky-400 transition duration-300 group-hover:bg-royal-600 group-hover:text-white">
            <x-icon :name="$icon" class="h-5 w-5" />
        </div>

        <span @class([
            'inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] ring-1 ring-inset',
            'bg-emerald-50 text-emerald-700 ring-emerald-500/20' => $plan === 'basic',
            'bg-royal-50 text-[#1E40AF] ring-royal-600/20' => $plan === 'professional',
        ])>
            {{ $planLabel }}
        </span>
    </div>

    <h3 class="font-display text-lg font-bold tracking-tight text-navy-900">{{ $title }}</h3>
    <p class="mt-3 text-sm leading-relaxed text-mute">{{ $description }}</p>
</article>
