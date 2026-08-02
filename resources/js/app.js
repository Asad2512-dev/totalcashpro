import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('siteNav', (options = {}) => {
        const config = typeof options === 'string'
            ? { initialMatch: options, isHome: false }
            : options;

        return {
            open: false,
            scrolled: false,
            activeMatch: config.initialMatch || '',
            scrollSpyEnabled: Boolean(config.isHome),
            sectionMatches: [
                { id: 'features', match: 'features' },
                { id: 'solutions', match: 'solutions' },
                { id: 'pricing', match: 'pricing' },
            ],
            ticking: false,
            observer: null,

            init() {
                this.syncFromLocation();
                this.onScroll();
                this.bindScrollSpy();

                window.addEventListener(
                    'scroll',
                    () => {
                        if (this.ticking) {
                            return;
                        }

                        this.ticking = true;
                        window.requestAnimationFrame(() => {
                            this.onScroll();
                            this.ticking = false;
                        });
                    },
                    { passive: true },
                );

                window.addEventListener('hashchange', () => this.syncFromLocation());
            },

            isHomePath() {
                if (config.isHome) {
                    return true;
                }

                const path = window.location.pathname.replace(/\/+$/, '') || '/';
                const homePath = new URL(document.querySelector('link[rel="canonical"]')?.href || window.location.origin, window.location.origin)
                    .pathname
                    .replace(/\/+$/, '') || '/';

                return path === '/' || path === '' || path === homePath || path.endsWith('/public');
            },

            syncFromLocation() {
                const path = window.location.pathname.replace(/\/+$/, '') || '/';
                const hash = window.location.hash.replace('#', '');

                this.scrollSpyEnabled = this.isHomePath();

                if (path.endsWith('/about')) {
                    this.activeMatch = 'about';
                    this.teardownScrollSpy();
                    return;
                }

                if (path.endsWith('/contact')) {
                    this.activeMatch = 'contact';
                    this.teardownScrollSpy();
                    return;
                }

                if (path.includes('/request-access') || path.endsWith('/request-demo')) {
                    this.activeMatch = 'request-demo';
                    this.teardownScrollSpy();
                    return;
                }

                if (this.scrollSpyEnabled) {
                    if (['features', 'solutions', 'pricing'].includes(hash)) {
                        this.activeMatch = hash;
                    }

                    this.updateActiveFromScroll();
                    this.bindScrollSpy();
                    return;
                }

                this.activeMatch = config.initialMatch || '';
                this.teardownScrollSpy();
            },

            isActive(match) {
                return this.activeMatch === match;
            },

            setMatch(match) {
                this.activeMatch = match;
            },

            onScroll() {
                this.scrolled = window.scrollY > 16;

                if (this.scrollSpyEnabled) {
                    this.updateActiveFromScroll();
                }
            },

            bindScrollSpy() {
                this.teardownScrollSpy();

                if (! this.scrollSpyEnabled || typeof IntersectionObserver === 'undefined') {
                    return;
                }

                this.observer = new IntersectionObserver(
                    (entries) => {
                        const visible = entries
                            .filter((entry) => entry.isIntersecting)
                            .sort((a, b) => b.intersectionRatio - a.intersectionRatio);

                        if (visible.length > 0) {
                            const id = visible[0].target.id;
                            const match = this.sectionMatches.find((section) => section.id === id)?.match;

                            if (match) {
                                this.activeMatch = match;
                            }

                            return;
                        }

                        if (window.scrollY < 80) {
                            this.activeMatch = 'home';
                        }
                    },
                    {
                        root: null,
                        rootMargin: '-20% 0px -55% 0px',
                        threshold: [0.1, 0.25, 0.5],
                    },
                );

                this.sectionMatches.forEach((section) => {
                    const element = document.getElementById(section.id);

                    if (element) {
                        this.observer.observe(element);
                    }
                });
            },

            teardownScrollSpy() {
                if (this.observer) {
                    this.observer.disconnect();
                    this.observer = null;
                }
            },

            updateActiveFromScroll() {
                const marker = 140;
                let current = '';

                for (const section of this.sectionMatches) {
                    const element = document.getElementById(section.id);

                    if (! element) {
                        continue;
                    }

                    if (element.getBoundingClientRect().top - marker <= 0) {
                        current = section.match;
                    }
                }

                if (current) {
                    this.activeMatch = current;
                    return;
                }

                if (window.scrollY < 80) {
                    this.activeMatch = 'home';
                }
            },

            close() {
                this.open = false;
            },
        };
    });

    Alpine.data('adminShell', () => ({
        sidebarOpen: false,
        commandOpen: false,
        commandQuery: '',
        searchResults: [],
        // Default expanded; only collapse when user explicitly chose it.
        collapsed: window.localStorage.getItem('tcp-admin-collapsed') === '1',
        dark: window.localStorage.getItem('tcp-admin-theme') === 'dark',

        init() {
            // Recover from a previously broken collapsed/cloak state.
            if (window.localStorage.getItem('tcp-admin-sidebar-v') !== '2') {
                window.localStorage.setItem('tcp-admin-collapsed', '0');
                window.localStorage.setItem('tcp-admin-sidebar-v', '2');
                this.collapsed = false;
            }

            this.$watch('dark', (value) => {
                window.localStorage.setItem('tcp-admin-theme', value ? 'dark' : 'light');
            });

            this.$watch('collapsed', (value) => {
                window.localStorage.setItem('tcp-admin-collapsed', value ? '1' : '0');
            });

            this.$watch('commandOpen', (value) => {
                if (! value) {
                    this.commandQuery = '';
                    this.searchResults = [];
                }
            });

            window.addEventListener('keydown', (event) => {
                if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                    event.preventDefault();
                    this.commandOpen = true;
                }
            });
        },
    }));

    Alpine.data('adminSettings', () => ({
        tab: 'General',
    }));

    Alpine.data('adminBulkTable', () => ({
        selected: [],
        toggle(id) {
            if (this.selected.includes(id)) {
                this.selected = this.selected.filter((item) => item !== id);
            } else {
                this.selected.push(id);
            }
        },
        toggleAll(ids) {
            this.selected = this.selected.length === ids.length ? [] : [...ids];
        },
    }));
});

Alpine.start();

const revealObserver = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    },
    {
        threshold: 0.14,
        rootMargin: '0px 0px -48px 0px',
    },
);

document.querySelectorAll('[data-reveal]').forEach((element) => {
    element.classList.add('reveal');
    revealObserver.observe(element);
});

document.querySelectorAll('.btn-ripple').forEach((button) => {
    button.addEventListener('pointerdown', (event) => {
        const rect = button.getBoundingClientRect();
        button.style.setProperty('--ripple-x', `${event.clientX - rect.left}px`);
        button.style.setProperty('--ripple-y', `${event.clientY - rect.top}px`);
    });
});
