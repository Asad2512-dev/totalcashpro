<section id="smart-kiosk" class="scroll-mt-24 overflow-hidden bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-900 py-20 text-white lg:py-28" aria-labelledby="smart-kiosk-heading">
    <x-container>
        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-16">
            <div data-reveal>
                <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-emerald-100 ring-1 ring-inset ring-white/20">
                    Headline Feature
                </span>
                <h2 id="smart-kiosk-heading" class="mt-4 font-display text-3xl font-extrabold tracking-[-0.03em] sm:text-4xl lg:text-[2.75rem]">
                    Smart Attendance Kiosk
                </h2>
                <p class="mt-4 text-base leading-relaxed text-emerald-50/90 sm:text-lg">
                    A dedicated PIN terminal for your entrance — not another admin page. Staff never log in. The system automatically clocks them in or out. Business admins launch once with a secure branch URL.
                </p>
                <ul class="mt-8 space-y-3 text-sm text-emerald-50/95">
                    <li class="flex gap-3"><span class="mt-0.5 text-emerald-200">✓</span> Secure token URL per branch — <code class="rounded bg-black/20 px-1.5 py-0.5 text-xs">/kiosk/{token}</code></li>
                    <li class="flex gap-3"><span class="mt-0.5 text-emerald-200">✓</span> Auto clock-in / clock-out from PIN — no buttons for staff</li>
                    <li class="flex gap-3"><span class="mt-0.5 text-emerald-200">✓</span> Dedicated kiosk session — stays active until admin exits</li>
                    <li class="flex gap-3"><span class="mt-0.5 text-emerald-200">✓</span> Full audit log — device, IP, timestamp on every action</li>
                    <li class="flex gap-3"><span class="mt-0.5 text-emerald-200">✓</span> Hold logo 5 seconds + admin password to close</li>
                </ul>
                <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                    <x-button href="{{ route('register') }}" variant="primary" size="lg">Start Free Trial</x-button>
                    <x-button href="{{ route('features') }}#smart-kiosk" variant="secondary" size="lg">See All Features</x-button>
                </div>
            </div>

            <div class="relative" data-reveal>
                <div class="rounded-[1.75rem] border border-white/20 bg-white/10 p-3 backdrop-blur-sm">
                    <div class="rounded-[1.25rem] bg-white p-5 text-gray-900 shadow-2xl">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-emerald-600">Harbour Kitchen</p>
                                <p class="font-display text-lg font-bold">Dockside Kiosk</p>
                            </div>
                            <p class="font-display text-xl font-bold tabular-nums text-gray-900">09:01:42</p>
                        </div>
                        <p class="mt-4 text-center text-sm text-gray-500">Enter your PIN</p>
                        <div class="mt-4 flex justify-center gap-2">
                            @foreach ([1, 2, 3, 4] as $i)
                                <div class="h-12 w-10 rounded-xl border-2 border-emerald-500 bg-emerald-500"></div>
                            @endforeach
                        </div>
                        <div class="mt-5 grid grid-cols-3 gap-2">
                            @foreach (['1','2','3','4','5','6','7','8','9','C','0','⌫'] as $key)
                                <div @class([
                                    'flex h-11 items-center justify-center rounded-xl text-lg font-bold',
                                    'bg-emerald-600 text-white' => $key === '0',
                                    'border border-gray-200 bg-gray-50 text-gray-700' => ! in_array($key, ['0'], true),
                                ])>{{ $key }}</div>
                            @endforeach
                        </div>
                        <div class="mt-5 rounded-2xl bg-emerald-50 p-4 text-center">
                            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Success</p>
                            <p class="mt-1 font-display text-lg font-bold text-gray-900">Welcome, Jamie!</p>
                            <p class="text-sm font-semibold text-emerald-700">Clocked In · 09:01 AM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-container>
</section>
