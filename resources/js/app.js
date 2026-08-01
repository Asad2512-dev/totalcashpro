import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('siteNav', (initialMatch = '') => ({
        open: false,
        scrolled: false,
        activeMatch: initialMatch,

        init() {
            this.syncFromLocation();
            this.onScroll();
            window.addEventListener('scroll', () => this.onScroll(), { passive: true });
            window.addEventListener('hashchange', () => this.syncFromLocation());
        },

        syncFromLocation() {
            const path = window.location.pathname.replace(/\/$/, '') || '/';
            const hash = window.location.hash.replace('#', '');

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

            if (path === '/' || path === '') {
                this.activeMatch = hash ? `home#${hash}` : 'home';
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
