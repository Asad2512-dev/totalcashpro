<header
    x-data="siteNav(@js(['initialMatch' => $currentMatch(), 'isHome' => $isHome()]))"
    class="fixed inset-x-0 top-0 z-50 border-b border-transparent transition duration-300"
    :class="scrolled ? 'nav-solid' : 'bg-transparent'"
>
    <x-container>
        <nav class="flex items-center justify-between gap-4 py-4" aria-label="Primary">
            <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center self-center" x-on:click="setMatch('home'); close()" aria-label="{{ brand_name() }} home">
                <x-brand-logo height="h-9" class="max-w-none" />
            </a>

            <div class="hidden items-center gap-8 lg:flex">
                @foreach ($links() as $link)
                    <a
                        href="{{ $link['href'] }}"
                        @click="setMatch(@js($link['match']))"
                        :class="isActive(@js($link['match']))
                            ? 'text-sm font-semibold text-primary-700'
                            : 'text-sm font-medium text-mute transition hover:text-gray-900'"
                        :aria-current="isActive(@js($link['match'])) ? 'page' : null"
                    >
                        <span class="relative inline-flex flex-col items-center">
                            {{ $link['label'] }}
                            <span
                                class="mt-1 h-0.5 w-full origin-center rounded-full bg-primary-600 transition duration-200"
                                :class="isActive(@js($link['match'])) ? 'scale-x-100 opacity-100' : 'scale-x-50 opacity-0'"
                                aria-hidden="true"
                            ></span>
                        </span>
                    </a>
                @endforeach
            </div>

            <div class="hidden items-center gap-2 lg:flex">
                <x-button :href="route('login')" variant="ghost" size="sm">Login</x-button>
                <x-button :href="route('register')" variant="primary" size="sm">Start Free Trial</x-button>
            </div>

            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-line bg-snow/80 text-gray-900 lg:hidden"
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
                        ? 'rounded-2xl bg-primary-50 px-4 py-3 text-sm font-semibold text-primary-700'
                        : 'rounded-2xl px-4 py-3 text-sm font-medium text-gray-800 hover:bg-paper'"
                    :aria-current="isActive(@js($link['match'])) ? 'page' : null"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
            <div class="mt-2 flex flex-col gap-2 border-t border-line pt-4">
                <x-button :href="route('login')" variant="outline" class="w-full" x-on:click="close()">Login</x-button>
                <x-button :href="route('register')" variant="primary" class="w-full" x-on:click="close()">Start Free Trial</x-button>
            </div>
        </x-container>
    </div>
</header>
