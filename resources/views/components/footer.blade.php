<footer class="relative overflow-hidden border-t border-white/10 bg-[#0F172A] text-white">
    <x-container class="relative py-16 lg:py-20">
        <div class="grid gap-12 lg:grid-cols-[1.35fr_repeat(4,1fr)]">
            <div class="max-w-sm">
                <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center rounded-xl bg-white px-3 py-2" aria-label="{{ brand_name() }} home">
                    <x-brand-logo height="h-8" class="max-w-none" />
                </a>
                <p class="mt-5 text-sm leading-relaxed text-white/70">
                    {{ brand_tagline() }}
                </p>
                <p class="mt-4 text-sm font-semibold text-primary-300">From £19.99/month · Cloud SaaS</p>
                <div class="mt-6 flex items-center gap-2">
                    @foreach (config('totalcashpro.social') as $network => $url)
                        <a
                            href="{{ $url }}"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/15 bg-white/5 text-[11px] font-bold uppercase tracking-wide text-white/80 transition hover:bg-white/10 hover:text-white"
                            aria-label="{{ ucfirst($network) }}"
                        >
                            {{ strtoupper(substr($network, 0, 2)) }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="font-display text-xs font-bold uppercase tracking-[0.18em] text-white/50">Quick Links</h3>
                <ul class="mt-4 space-y-3 text-sm text-white/75">
                    <li><a class="transition hover:text-white" href="{{ route('home') }}#features">Features</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('home') }}#solutions">Solutions</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('home') }}#pricing">Pricing</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('request-demo') }}">Request Demo</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-display text-xs font-bold uppercase tracking-[0.18em] text-white/50">Resources</h3>
                <ul class="mt-4 space-y-3 text-sm text-white/75">
                    <li><a class="transition hover:text-white" href="{{ route('home') }}#faq">FAQ</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('home') }}#solutions">Solutions</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('home') }}#workflow">Workflow</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('home') }}#why">Why TotalCashPro</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-display text-xs font-bold uppercase tracking-[0.18em] text-white/50">Company</h3>
                <ul class="mt-4 space-y-3 text-sm text-white/75">
                    <li><a class="transition hover:text-white" href="{{ route('about') }}">About</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('contact') }}">Contact</a></li>
                    <li><a class="transition hover:text-white" href="mailto:{{ config('totalcashpro.support_email') }}">Support</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-display text-xs font-bold uppercase tracking-[0.18em] text-white/50">Legal</h3>
                <ul class="mt-4 space-y-3 text-sm text-white/75">
                    <li><a class="transition hover:text-white" href="{{ route('privacy') }}">Privacy</a></li>
                    <li><a class="transition hover:text-white" href="{{ route('terms') }}">Terms</a></li>
                </ul>
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-3 border-t border-white/15 pt-6 text-sm text-white/55 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ brand_name() }}. All rights reserved.</p>
            <p>Cloud SaaS for restaurants and retail businesses.</p>
        </div>
    </x-container>
</footer>
