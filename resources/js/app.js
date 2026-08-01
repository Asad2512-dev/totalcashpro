import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('siteNav', (initialMatch = '') => ({
        open: false,
        scrolled: false,
        activeMatch: initialMatch,
        scrollSpyEnabled: false,
        sectionMatches: [
            { id: 'features', match: 'home#features' },
            { id: 'workflow', match: 'home#workflow' },
            { id: 'pricing', match: 'home#pricing' },
        ],
        ticking: false,

        init() {
            this.scrollSpyEnabled = this.isHomePath();
            this.syncFromLocation();
            this.onScroll();

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
            const path = window.location.pathname.replace(/\/$/, '') || '/';

            return path === '/' || path === '';
        },

        syncFromLocation() {
            const path = window.location.pathname.replace(/\/$/, '') || '/';
            const hash = window.location.hash.replace('#', '');

            this.scrollSpyEnabled = path === '/' || path === '';

            if (path === '/about') {
                this.activeMatch = 'about';
                return;
            }

            if (path === '/contact') {
                this.activeMatch = 'contact';
                return;
            }

            if (path.startsWith('/request-access')) {
                this.activeMatch = 'request-access';
                return;
            }

            if (this.scrollSpyEnabled) {
                if (hash) {
                    this.activeMatch = `home#${hash}`;
                }

                this.updateActiveFromScroll();
                return;
            }

            this.activeMatch = initialMatch || '';
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

        updateActiveFromScroll() {
            const marker = 120;
            let current = '';

            for (const section of this.sectionMatches) {
                const element = document.getElementById(section.id);

                if (!element) {
                    continue;
                }

                const top = element.getBoundingClientRect().top;

                if (top - marker <= 0) {
                    current = section.match;
                }
            }

            if (current) {
                this.activeMatch = current;
                return;
            }

            // Near the top of the homepage — no section link active yet.
            if (window.scrollY < 80) {
                this.activeMatch = 'home';
            }
        },

        close() {
            this.open = false;
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
