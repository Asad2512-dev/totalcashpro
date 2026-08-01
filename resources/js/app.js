import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('siteNav', () => ({
        open: false,
        scrolled: false,

        init() {
            this.onScroll();
            window.addEventListener('scroll', () => this.onScroll(), { passive: true });
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
