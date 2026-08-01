<header
    x-data="siteNav(@js($currentMatch()))"
    class="fixed inset-x-0 top-0 z-50 border-b border-transparent transition duration-300"
    :class="scrolled ? 'nav-solid' : 'bg-transparent'"
>
    <x-container>
        <nav class="flex items-center justify-between gap-4 py-4" aria-label="Primary">
            <a href="{{ route('home') }}" class="group flex items-center gap-2.5" x-on:click="close()">
                <span class="relative inline-flex h-10 w-10 items-center justify-center overflow-hidden rounded-2xl bg-navy-900">
                    <span class="absolute inset-0 bg-gradient-to-br from-royal-600/40 via-transparent to-emerald-500/30"></span>
                    <span class="relative font-display text-sm font-extrabold tracking-tight text-white">TC</span>
                </span>
                <span class="font-display text-lg font-extrabold tracking-tight text-navy-900">
                    {{ brand_name() }}
                </span>
            </a>

            <div class="hidden items-center gap-8 lg:flex">
                @foreach ($links() as $link)
                    <a
                        href="{{ $link['href'] }}"
                        @click="setMatch(@js($link['match']))"
                        :class="isActive(@js($link['match']))
                            ? 'text-sm font-semibold text-navy-900'
                            : 'text-sm font-medium text-mute transition hover:text-navy-900'"
                        :aria-current="isActive(@js($link['match'])) ? 'page' : null"
                    >
                        <span class="relative inline-flex flex-col items-center">
                            {{ $link['label'] }}
                            <span
                                class="mt-1 h-0.5 w-full rounded-full bg-royal-600 transition"
                                :class="isActive(@js($link['match'])) ? 'opacity-100' : 'opacity-0'"
                                aria-hidden="true"
                            ></span>
                        </span>
                    </a>
                @endforeach
            </div>

            <div class="hidden items-center gap-2 lg:flex">
                <x-button href="{{ route('home') }}#pricing" variant="ghost" size="sm">Choose Your Plan</x-button>
                <x-button
                    :href="route('request-access')"
                    variant="primary"
                    size="sm"
                    @class([
                        'ring-2 ring-royal-600/30 ring-offset-2' => request()->routeIs('request-access*'),
                    ])
                >
                    Request Access
                </x-button>
            </div>

            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-line bg-snow/80 text-navy-900 lg:hidden"
                @click="open = !open"
                :aria-expanded="open.toString()"
                aria-controls="mobile-nav"
                aria-label="Toggle navigation"
            >
                <svg x-show="!open" class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M3.5 6h13M3.5 10h13M3.5 14h13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
                <svg x-cloak x-show="open" class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M5 5l10 10M15 5 5 15" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </button>
        </nav>
    </x-container>

    <div
        id="mobile-nav"
        x-cloak
        x-show="open"
        x-transition.opacity
        class="border-t border-line bg-snow/95 backdrop-blur-xl lg:hidden"
    >
        <x-container class="flex flex-col gap-1 py-4">
            @foreach ($links() as $link)
                <a
                    href="{{ $link['href'] }}"
                    @click="setMatch(@js($link['match'])); close()"
                    :class="isActive(@js($link['match']))
                        ? 'rounded-2xl bg-royal-50 px-4 py-3 text-sm font-semibold text-royal-700'
                        : 'rounded-2xl px-4 py-3 text-sm font-medium text-navy-800 hover:bg-paper'"
                    :aria-current="isActive(@js($link['match'])) ? 'page' : null"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
            <div class="mt-2 flex flex-col gap-2 border-t border-line pt-4">
                <x-button href="{{ route('home') }}#pricing" variant="outline" class="w-full" x-on:click="close()">Choose Your Plan</x-button>
                <x-button :href="route('request-access')" variant="primary" class="w-full" x-on:click="close()">Request Access</x-button>
            </div>
        </x-container>
    </div>
</header>
