@props([
    'title' => 'Filters',
    'activeCount' => 0,
])

<div
    {{ $attributes }}
    x-data="{
        filterOpen: false,
        isDesktop: false,
        init() {
            this.media = window.matchMedia('(min-width: 1024px)');
            this.isDesktop = this.media.matches;
            this.media.addEventListener('change', (event) => {
                this.isDesktop = event.matches;
                if (event.matches) {
                    this.filterOpen = false;
                }
            });
        },
        get panelVisible() {
            return this.filterOpen || this.isDesktop;
        },
    }"
    @keydown.escape.window="filterOpen = false"
>
    <button
        type="button"
        class="admin-touch-target mb-3 inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm lg:hidden dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
        @click="filterOpen = true"
    >
        <x-admin.icon name="search" class="h-4 w-4" />
        {{ $title }}
        @if ($activeCount > 0)
            <span class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-primary-600 px-1.5 text-xs font-bold text-white">{{ $activeCount }}</span>
        @endif
    </button>

    <div
        x-cloak
        x-show="filterOpen && !isDesktop"
        x-transition.opacity
        class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-[2px] lg:hidden"
        @click="filterOpen = false"
    ></div>

    <div
        x-cloak
        x-show="panelVisible"
        x-transition:enter="transition ease-out duration-250"
        x-transition:enter-start="translate-y-full lg:translate-y-0"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full lg:translate-y-0"
        class="report-filter-panel admin-card mb-4 p-4 print:hidden lg:static lg:max-h-none lg:overflow-visible lg:rounded-2xl lg:border lg:shadow-sm"
        :class="!isDesktop && filterOpen ? 'fixed inset-x-0 bottom-0 z-50 mb-0 flex max-h-[min(88dvh,36rem)] flex-col overflow-hidden rounded-t-2xl border-x-0 border-b-0 p-0 shadow-2xl' : ''"
    >
        <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-4 py-3 lg:hidden dark:border-gray-700">
            <h3 class="font-display text-base font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
            <button type="button" class="admin-touch-target rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" @click="filterOpen = false" aria-label="Close filters">
                <x-admin.icon name="x" class="h-4 w-4" />
            </button>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-4 lg:overflow-visible lg:px-0" :class="!isDesktop && filterOpen ? 'pb-2 pt-0' : ''">
            {{ $slot }}
        </div>

        @isset($footer)
            <div
                class="admin-filter-sheet-footer shrink-0 px-4 lg:mt-4 lg:flex lg:border-0 lg:bg-transparent lg:px-0 lg:p-0"
                :class="!isDesktop && filterOpen ? 'border-t border-gray-200 dark:border-gray-700' : ''"
            >
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
