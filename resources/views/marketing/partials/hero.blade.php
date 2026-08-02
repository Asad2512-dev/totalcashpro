<section class="relative overflow-hidden mesh-hero pt-28 pb-10 sm:pt-32 lg:pb-16" aria-labelledby="hero-heading">
    <div class="pointer-events-none absolute inset-0 dot-field opacity-40" aria-hidden="true"></div>

    <x-container class="relative">
        <div class="grid items-center gap-12 lg:grid-cols-[0.95fr_1.05fr] lg:gap-10 xl:gap-16">
            <div class="max-w-xl" data-reveal>
                <x-badge tone="emerald">Cloud software for restaurants & retail</x-badge>

                <h1 id="hero-heading" class="mt-5 font-display text-[2.45rem] font-extrabold leading-[1.08] tracking-[-0.04em] text-navy-900 sm:text-5xl lg:text-[3.35rem]">
                    Manage cash, staff and reports from one secure dashboard
                </h1>

                <p class="mt-5 text-lg font-semibold tracking-tight text-navy-800">
                    Built for restaurants, cafés, takeaways and retail businesses.
                </p>

                <p class="mt-4 max-w-lg text-base leading-relaxed text-mute sm:text-lg">
                    Choose Basic (£19.99/month) or Professional (£29.99/month), then start your free 14-day trial instantly.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <x-button href="{{ route('register') }}" variant="primary" size="lg">Start Your Free 14-Day Trial</x-button>
                    <x-button href="#features" variant="secondary" size="lg">View Features</x-button>
                </div>

                <dl class="mt-10 grid grid-cols-2 gap-x-6 gap-y-5 border-t border-line pt-8 sm:grid-cols-4 lg:grid-cols-2 xl:grid-cols-4">
                    @foreach ($heroStats as $stat)
                        <div>
                            <dt class="text-[11px] font-semibold uppercase tracking-[0.14em] text-mute">{{ $stat['label'] }}</dt>
                            <dd class="mt-1 font-display text-2xl font-extrabold tracking-tight text-navy-900">{{ $stat['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="relative mx-auto w-full max-w-xl lg:max-w-none" data-reveal>
                <div class="float-b absolute -left-2 top-10 z-20 hidden w-44 rounded-2xl border border-line bg-snow p-3 shadow-lift sm:block lg:-left-6">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-mute">Cash balance</p>
                    <p class="mt-1 font-display text-xl font-extrabold text-navy-900">£4,286.50</p>
                    <p class="mt-1 text-xs font-medium text-emerald-600">Till A · Balanced</p>
                </div>

                <div class="float-a absolute -right-1 bottom-20 z-20 hidden w-48 rounded-2xl border border-line bg-navy-900 p-3 text-white shadow-soft sm:block lg:-right-4">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-white/50">Active staff</p>
                        <span class="h-2 w-2 rounded-full bg-sky-400"></span>
                    </div>
                    <p class="mt-1 font-display text-xl font-extrabold">18 on floor</p>
                    <p class="mt-1 text-xs text-white/55">Professional · Unlimited</p>
                </div>

                <div class="relative overflow-hidden rounded-[1.75rem] border border-line bg-snow p-3 shadow-soft sm:p-4">
                    <div class="rounded-[1.25rem] border border-line bg-paper p-4 sm:p-5">
                        <div class="flex items-center justify-between gap-3 border-b border-line pb-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-royal-600">Operations dashboard</p>
                                <p class="mt-1 font-display text-lg font-bold text-navy-900">Today’s Overview</p>
                            </div>
                            <span class="rounded-xl bg-sky-50 px-3 py-1 text-xs font-semibold text-navy-800">Cloud</span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-navy-900 p-4 text-white">
                                <p class="text-xs text-white/55">Cash balance</p>
                                <p class="mt-2 font-display text-2xl font-extrabold">£4,286</p>
                            </div>
                            <div class="rounded-2xl bg-emerald-50 p-4">
                                <p class="text-xs text-emerald-600/80">Today’s cash up</p>
                                <p class="mt-2 font-display text-2xl font-extrabold text-navy-900">£1,942</p>
                            </div>
                            <div class="rounded-2xl border border-line bg-snow p-4">
                                <p class="text-xs text-mute">Active staff</p>
                                <p class="mt-2 font-display text-2xl font-extrabold text-navy-900">18</p>
                            </div>
                            <div class="rounded-2xl border border-line bg-snow p-4">
                                <p class="text-xs text-mute">Inventory alerts</p>
                                <p class="mt-2 font-display text-2xl font-extrabold text-warning">05</p>
                            </div>
                        </div>

                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-line bg-snow p-4">
                                <p class="text-xs font-semibold text-navy-800">Attendance summary</p>
                                <div class="mt-3 space-y-2 text-xs text-mute">
                                    <div class="flex justify-between"><span>Clocked in</span><span class="font-semibold text-ink">18</span></div>
                                    <div class="flex justify-between"><span>On break</span><span class="font-semibold text-ink">2</span></div>
                                    <div class="flex justify-between"><span>Late today</span><span class="font-semibold text-ink">1</span></div>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-line bg-snow p-4">
                                <p class="text-xs font-semibold text-navy-800">Recent transactions</p>
                                <div class="mt-3 space-y-2 text-xs text-mute">
                                    <div class="flex justify-between"><span>Cash drop</span><span class="font-semibold text-ink">£200.00</span></div>
                                    <div class="flex justify-between"><span>Supplier pay</span><span class="font-semibold text-ink">£84.50</span></div>
                                    <div class="flex justify-between"><span>Till open</span><span class="font-semibold text-success">£150.00</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 rounded-2xl border border-line bg-navy-50/70 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-xs font-semibold text-navy-800">Weekly reports</p>
                                <p class="text-xs font-medium text-emerald-600">+8.2% vs last week</p>
                            </div>
                            <div class="flex h-20 items-end gap-1.5">
                                <div class="h-[42%] flex-1 rounded-t-md bg-royal-600/80"></div>
                                <div class="h-[58%] flex-1 rounded-t-md bg-royal-600/80"></div>
                                <div class="h-[50%] flex-1 rounded-t-md bg-royal-600/80"></div>
                                <div class="h-[72%] flex-1 rounded-t-md bg-royal-600/80"></div>
                                <div class="h-[64%] flex-1 rounded-t-md bg-royal-600/80"></div>
                                <div class="h-[88%] flex-1 rounded-t-md bg-emerald-500"></div>
                                <div class="h-[76%] flex-1 rounded-t-md bg-royal-600/80"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-container>
</section>
