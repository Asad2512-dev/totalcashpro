import Alpine from 'alpinejs';
import { registerStaffPwa } from './staff-pwa';

window.Alpine = Alpine;

const THEME_STORAGE_KEY = 'tcp-admin-theme';

function prefersDarkMode() {
    try {
        const stored = window.localStorage.getItem(THEME_STORAGE_KEY);

        if (stored === 'dark') {
            return true;
        }

        if (stored === 'light') {
            return false;
        }
    } catch (error) {
        // localStorage may be unavailable in private browsing.
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

function applyThemeClass(isDark) {
    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';

    const meta = document.querySelector('meta[name="theme-color"]');

    if (meta) {
        meta.setAttribute('content', isDark ? '#030712' : '#16A34A');
    }
}

window.tcpApplyTheme = applyThemeClass;
window.tcpPrefersDarkMode = prefersDarkMode;

document.addEventListener('alpine:init', () => {
    Alpine.store('authUi', {
        submitting: false,

        start() {
            this.submitting = true;
        },

        stop() {
            this.submitting = false;
        },
    });

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

    function csrfToken(fallback = '') {
        return document.querySelector('meta[name="csrf-token"]')?.content || fallback;
    }

    async function readJsonResponse(response) {
        const contentType = response.headers.get('content-type') || '';

        if (contentType.includes('application/json')) {
            return response.json();
        }

        if (response.status === 419) {
            throw { message: 'Your session expired. Please refresh the page and try again.' };
        }

        if (response.status === 403) {
            throw { message: 'You do not have permission to perform this action.' };
        }

        const snippet = (await response.text()).replace(/\s+/g, ' ').trim().slice(0, 160);
        throw { message: snippet || `Request failed (${response.status}).` };
    }

    Alpine.data('copyText', (text = '') => ({
        text,
        copied: false,
        timer: null,

        async copy() {
            const value = String(this.text || '');
            if (value === '') {
                return;
            }

            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(value);
                } else {
                    this.fallbackCopy(value);
                }
                this.showCopied();
            } catch {
                try {
                    this.fallbackCopy(value);
                    this.showCopied();
                } catch {
                    this.copied = false;
                }
            }
        },

        fallbackCopy(value) {
            const input = document.createElement('textarea');
            input.value = value;
            input.setAttribute('readonly', '');
            input.style.position = 'fixed';
            input.style.opacity = '0';
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
        },

        showCopied() {
            this.copied = true;
            if (this.timer) {
                clearTimeout(this.timer);
            }
            this.timer = setTimeout(() => {
                this.copied = false;
            }, 2000);
        },
    }));

    Alpine.data('adminShell', () => ({
        sidebarOpen: false,
        commandOpen: false,
        mobileMoreOpen: false,
        commandQuery: '',
        searchResults: [],
        logoutConfirm: false,
        collapsed: false,
        dark: prefersDarkMode(),

        init() {
            applyThemeClass(this.dark);

            window.localStorage.setItem('tcp-admin-collapsed', '0');

            this.$watch('dark', (value) => {
                window.localStorage.setItem(THEME_STORAGE_KEY, value ? 'dark' : 'light');
                applyThemeClass(value);
            });

            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');
            const onSystemThemeChange = () => {
                try {
                    const stored = window.localStorage.getItem(THEME_STORAGE_KEY);

                    if (stored !== 'dark' && stored !== 'light') {
                        this.dark = systemTheme.matches;
                        applyThemeClass(this.dark);
                    }
                } catch (error) {
                    // Ignore storage errors.
                }
            };

            if (typeof systemTheme.addEventListener === 'function') {
                systemTheme.addEventListener('change', onSystemThemeChange);
            } else if (typeof systemTheme.addListener === 'function') {
                systemTheme.addListener(onSystemThemeChange);
            }

            window.addEventListener('storage', (event) => {
                if (event.key === THEME_STORAGE_KEY) {
                    this.dark = prefersDarkMode();
                    applyThemeClass(this.dark);
                }
            });

            if ('scrollRestoration' in history) {
                history.scrollRestoration = 'manual';
            }

            this.$nextTick(() => {
                this.restoreSidebarScroll();
            });

            this.$watch('commandOpen', (value) => {
                if (! value) {
                    this.commandQuery = '';
                    this.searchResults = [];
                }

                this.syncBodyScrollLock();
            });

            this.$watch('sidebarOpen', () => {
                this.syncBodyScrollLock();
            });

            this.$watch('logoutConfirm', () => {
                this.syncBodyScrollLock();
            });

            this.$watch('mobileMoreOpen', () => {
                this.syncBodyScrollLock();
            });

            this.$el.addEventListener('confirm-logout', () => {
                this.logoutConfirm = true;
            });

            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    if (this.logoutConfirm) {
                        this.cancelLogout();
                        return;
                    }

                    if (this.commandOpen) {
                        this.commandOpen = false;
                        return;
                    }

                    if (this.mobileMoreOpen) {
                        this.mobileMoreOpen = false;
                        return;
                    }

                    if (this.sidebarOpen) {
                        this.closeSidebar();
                    }

                    return;
                }

                if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                    event.preventDefault();
                    this.commandOpen = true;
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.sidebarOpen = false;
                    this.syncBodyScrollLock();
                }
            });
        },

        syncBodyScrollLock() {
            const locked = this.sidebarOpen || this.commandOpen || this.logoutConfirm || this.mobileMoreOpen;
            document.documentElement.classList.toggle('admin-scroll-lock', locked);
        },

        openSidebar() {
            this.sidebarOpen = true;
        },

        closeSidebar() {
            this.sidebarOpen = false;
        },

        toggleSidebar() {
            this.sidebarOpen = ! this.sidebarOpen;
        },

        requestLogout() {
            this.logoutConfirm = true;
        },

        cancelLogout() {
            this.logoutConfirm = false;
        },

        saveSidebarScroll() {
            const nav = document.getElementById('admin-sidebar-nav');

            if (nav) {
                sessionStorage.setItem('tcp-admin-sidebar-scroll', String(nav.scrollTop));
            }
        },

        restoreSidebarScroll() {
            const nav = document.getElementById('admin-sidebar-nav');

            if (! nav) {
                return;
            }

            const saved = sessionStorage.getItem('tcp-admin-sidebar-scroll');

            if (saved !== null && saved !== '') {
                nav.scrollTop = Number.parseInt(saved, 10) || 0;

                return;
            }

            const active = nav.querySelector('[data-sidebar-active]');

            if (active) {
                active.scrollIntoView({ block: 'nearest', inline: 'nearest' });
            }
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

    Alpine.data('cashUpWizard', (config = {}) => ({
        view: 'cashup',
        step: Number(config.initialStep || 0),
        steps: ['Coins', 'Notes', 'Cards', 'Expenses', 'Online'],
        stepDescriptions: [
            'Enter the quantity of each coin to calculate your coins total.',
            'Enter the quantity of each note and any extra coin amount.',
            'Enter card machine totals and any refunds for this shift.',
            'Enter expense descriptions and amounts for this shift.',
            'Enter the amount for each online order platform.',
        ],
        date: config.initialDate,
        shift: config.initialShift,
        coins: config.coins || [],
        notes: config.notes || [],
        cards: config.cards || [],
        refundAmount: config.refundAmount || 0,
        expenses: config.expenses || [],
        platforms: config.platforms || [],
        deductions: config.deductions || [],
        loading: false,
        error: false,
        statusMessage: '',
        saveUrl: config.saveUrl,
        deductionsUrl: config.deductionsUrl,
        csrf: config.csrf,
        openingFloat: config.openingFloat || 100,
        cashDrawerId: config.cashDrawerId || null,
        varianceReason: config.varianceReason || '',

        money(value) {
            return '£' + Number(value || 0).toFixed(2);
        },

        get coinsTotal() {
            return this.coins.reduce((sum, row) => sum + (Number(row.value) * Number(row.qty || 0)), 0);
        },
        get notesTotal() {
            return this.notes.reduce((sum, row) => {
                if (row.is_qty) {
                    return sum + (Number(row.value) * Number(row.qty || 0));
                }
                return sum + Number(row.amount || 0);
            }, 0);
        },
        get cardsTotal() {
            const machines = this.cards.reduce((sum, row) => sum + Number(row.amount || 0), 0);
            return machines - Number(this.refundAmount || 0);
        },
        get expensesTotal() {
            return this.expenses.reduce((sum, row) => sum + Number(row.amount || 0), 0);
        },
        get onlineTotal() {
            return this.platforms.reduce((sum, row) => sum + Number(row.amount || 0), 0);
        },
        get deductionsTotal() {
            return this.deductions.reduce((sum, row) => sum + Number(row.amount || 0), 0);
        },
        get actualCash() {
            return this.coinsTotal + this.notesTotal;
        },
        get cashSales() {
            return Math.max(0, this.actualCash - Number(this.openingFloat || 0) + this.expensesTotal);
        },
        get expectedCash() {
            return Number(this.openingFloat || 0) + this.cashSales - this.expensesTotal;
        },
        get variance() {
            return this.actualCash - this.expectedCash;
        },
        get shiftTotal() {
            return this.coinsTotal + this.notesTotal + this.cardsTotal + this.expensesTotal + this.onlineTotal;
        },

        addCardMachine() {
            this.cards.push({
                payment_type: `Card Machine ${this.cards.length + 1}`,
                type: 'machine',
                amount: 0,
            });
        },

        goToStep(index) {
            if (index < 0 || index >= this.steps.length) {
                return;
            }
            this.step = index;
            this.syncStep();
        },

        stepTabTotal(index) {
            const totals = [
                this.coinsTotal,
                this.notesTotal,
                this.cardsTotal,
                this.expensesTotal,
                this.onlineTotal,
            ];

            return this.money(totals[index] ?? 0);
        },

        syncStep() {
            const url = new URL(window.location.href);
            url.searchParams.set('step', String(this.step));
            url.searchParams.set('date', this.date);
            url.searchParams.set('shift', this.shift);
            url.searchParams.set('view', 'cashup');
            window.history.replaceState({}, '', url.toString());
        },

        reloadForDate() {
            const params = new URLSearchParams({
                date: this.date,
                shift: this.shift,
                view: new URLSearchParams(window.location.search).get('view') || 'cashup',
                step: String(this.step || 0),
            });
            window.location = `${window.location.pathname}?${params.toString()}`;
        },

        async postJson(url, body) {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            });
            const data = await res.json().catch(() => ({}));
            if (! res.ok) {
                const error = new Error(data.message || 'Save failed');
                error.data = data;
                throw error;
            }
            return data;
        },

        async saveCashUp(overwrite = false) {
            this.loading = true;
            this.error = false;
            this.statusMessage = '';

            const cards = this.cards
                .filter((row) => Number(row.amount || 0) > 0)
                .map((row) => ({
                    payment_type: row.payment_type,
                    type: 'machine',
                    amount: Number(row.amount || 0),
                }));
            if (Number(this.refundAmount || 0) > 0) {
                cards.push({
                    payment_type: 'Refunds',
                    type: 'refund',
                    amount: Number(this.refundAmount || 0),
                });
            }

            try {
                await this.postJson(this.saveUrl, {
                    cashup_date: this.date,
                    shift: this.shift,
                    cash_drawer_id: this.cashDrawerId,
                    opening_float: Number(this.openingFloat || 0),
                    variance_reason: this.varianceReason || null,
                    overwrite,
                    coins: this.coins,
                    notes: this.notes,
                    cards,
                    expenses: this.expenses,
                    online: this.platforms,
                });
                this.statusMessage = 'Cash up saved.';
            } catch (e) {
                if (e.data?.code === 'ALREADY_EXISTS' && ! overwrite) {
                    if (window.confirm('Cash Up already exists for this date and shift. Overwrite it?')) {
                        return this.saveCashUp(true);
                    }
                }
                this.error = true;
                this.statusMessage = e.data?.errors?.cashup?.[0] || e.message || 'Unable to save cash up.';
            } finally {
                this.loading = false;
            }
        },

        async saveDeductions(overwrite = false) {
            this.loading = true;
            this.error = false;
            this.statusMessage = '';

            try {
                await this.postJson(this.deductionsUrl, {
                    cashup_date: this.date,
                    shift: this.shift,
                    overwrite,
                    deductions: this.deductions,
                });
                this.statusMessage = 'Platform deductions saved.';
            } catch (e) {
                if (e.data?.code === 'ALREADY_EXISTS' && ! overwrite) {
                    if (window.confirm('Platform deductions already exist for this shift. Overwrite them?')) {
                        return this.saveDeductions(true);
                    }
                }
                this.error = true;
                this.statusMessage = e.data?.errors?.cashup?.[0] || e.message || 'Unable to save deductions.';
            } finally {
                this.loading = false;
            }
        },
    }));

    Alpine.data('staffClock', (config = {}) => ({
        state: config.initialState || 'not_checked_in',
        userName: config.userName || '',
        hours: config.hours ?? null,
        breakEndsAt: config.breakEndsAt || null,
        message: '',
        statusMessage: '',
        error: false,
        loading: false,
        actionUrl: config.actionUrl,
        statusUrl: config.statusUrl,
        csrf: config.csrf,

        init() {
            this.message = this.stateMessage({
                state: this.state,
                break_ends_at: this.breakEndsAt,
            });
        },

        stateMessage(data) {
            if (data.state === 'auto_checked_in' || data.state === 'checked_in') {
                return 'You are currently clocked in.\nChoose an action below.';
            }
            if (data.state === 'on_break') {
                const ends = data.break_ends_at
                    ? new Date(data.break_ends_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    : '';
                return ends ? `You are on break until ${ends}.` : 'You are on break.';
            }
            return 'You are not clocked in yet.';
        },

        async act(action) {
            if (this.loading) {
                return;
            }
            this.loading = true;
            this.error = false;
            this.statusMessage = '';
            try {
                const res = await fetch(this.actionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ action }),
                });
                const data = await res.json().catch(() => ({}));
                if (! res.ok) {
                    throw new Error(data.message || data.errors?.action?.[0] || 'Action failed');
                }
                this.state = data.state;
                this.userName = data.user?.name || this.userName;
                this.hours = data.hours ?? this.hours;
                this.breakEndsAt = data.break_ends_at || null;
                this.message = this.stateMessage(data);
                this.statusMessage = 'Updated.';
            } catch (e) {
                this.error = true;
                this.statusMessage = e.message || 'Unable to update clock status.';
            } finally {
                this.loading = false;
            }
        },
    }));

    Alpine.data('clockKiosk', (config = {}) => ({
        pin: '',
        screen: 'pin',
        state: null,
        userName: '',
        message: '',
        error: false,
        loading: false,
        breakEndsAt: null,
        verifyUrl: config.verifyUrl,
        actionUrl: config.actionUrl,
        csrf: config.csrf,

        get availableActions() {
            if (this.state === 'checked_in' || this.state === 'auto_checked_in') {
                return [
                    { id: 'clock-out', label: 'Clock Out' },
                    { id: 'start-break', label: 'Start Break' },
                ];
            }
            if (this.state === 'on_break') {
                return [{ id: 'end-break', label: 'End Break' }];
            }
            if (this.state === 'not_checked_in') {
                return [{ id: 'clock-in', label: 'Clock In' }];
            }
            return [];
        },

        press(digit) {
            if (this.loading || this.pin.length >= 4) {
                return;
            }
            this.pin += String(digit);
            this.error = false;
            this.message = '';
            if (this.pin.length === 4) {
                this.verify();
            }
        },

        backspace() {
            this.pin = this.pin.slice(0, -1);
            this.error = false;
            this.message = '';
        },

        resetToPin() {
            this.pin = '';
            this.screen = 'pin';
            this.state = null;
            this.userName = '';
            this.message = '';
            this.error = false;
            this.breakEndsAt = null;
        },

        stateMessage(data) {
            if (data.state === 'auto_checked_in') {
                return 'You have been clocked in automatically.';
            }
            if (data.state === 'checked_in') {
                return 'You are currently clocked in.\nChoose an action below.';
            }
            if (data.state === 'on_break') {
                const ends = data.break_ends_at ? new Date(data.break_ends_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                return ends ? `You are on break until ${ends}.` : 'You are on break.';
            }
            if (data.state === 'not_checked_in') {
                return data.message || 'You are not clocked in yet.';
            }
            return 'Choose an action';
        },

        async verify() {
            if (this.pin.length !== 4 || this.loading) {
                return;
            }

            this.loading = true;
            this.error = false;
            this.message = '';

            try {
                const res = await fetch(this.verifyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ pin: this.pin }),
                });
                const data = await readJsonResponse(res);
                if (! res.ok) {
                    throw data;
                }
                this.state = data.state;
                this.userName = data.user?.name || '';
                this.breakEndsAt = data.break_ends_at || null;
                this.message = this.stateMessage(data);
                this.screen = 'action';
            } catch (e) {
                this.error = true;
                this.message = e?.errors?.pin?.[0] || e?.message || 'Invalid PIN.';
                this.state = null;
                this.userName = '';
                this.pin = '';
            } finally {
                this.loading = false;
            }
        },

        async act(action) {
            if (this.pin.length !== 4 || this.loading) {
                return;
            }

            this.loading = true;
            this.error = false;

            try {
                const res = await fetch(this.actionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ pin: this.pin, action }),
                });
                const data = await readJsonResponse(res);
                if (! res.ok) {
                    throw data;
                }
                this.state = data.state;
                this.userName = data.user?.name || '';
                this.breakEndsAt = data.break_ends_at || null;

                if (action === 'clock-out') {
                    this.message = 'Clocked out' + (data.hours != null ? ` · ${data.hours} hrs` : '');
                    setTimeout(() => this.resetToPin(), 1800);
                    return;
                }

                this.message = this.stateMessage(data);
            } catch (e) {
                this.error = true;
                this.message = e?.errors?.action?.[0] || e?.errors?.pin?.[0] || e?.message || 'Action failed.';
            } finally {
                this.loading = false;
            }
        },
    }));

    Alpine.data('attendanceKiosk', (config = {}) => ({
        pin: '',
        screen: 'welcome',
        state: null,
        userName: '',
        userAvatar: null,
        userInitials: '',
        message: '',
        statusMessage: '',
        error: false,
        loading: false,
        hoursToday: null,
        breakEndsAt: null,
        successTitle: '',
        successSubtitle: '',
        successTime: '',
        clockTime: '',
        clockDate: '',
        showExit: false,
        exitPassword: '',
        exitError: '',
        exitLoading: false,
        verifyUrl: config.verifyUrl,
        actionUrl: config.actionUrl,
        exitUrl: config.exitUrl,
        csrf: config.csrf,
        branchName: config.branchName || '',
        welcomeMessage: config.welcomeMessage || 'Welcome — please enter your PIN.',
        successSeconds: config.successSeconds || 4,
        showPhotos: config.showPhotos !== false,
        clockTimer: null,

        init() {
            this.tickClock();
            this.clockTimer = setInterval(() => this.tickClock(), 1000);
            this.$nextTick(() => this.$refs.pinInput?.focus());
        },

        destroy() {
            if (this.clockTimer) {
                clearInterval(this.clockTimer);
            }
        },

        tickClock() {
            const now = new Date();
            this.clockTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            this.clockDate = now.toLocaleDateString([], { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        },

        press(digit) {
            if (this.loading || this.pin.length >= 4) return;
            this.pin += String(digit);
            this.error = false;
            this.message = '';
            if (this.pin.length === 4) this.verify();
        },

        backspace() {
            this.pin = this.pin.slice(0, -1);
            this.error = false;
            this.message = '';
        },

        clearPin() {
            this.pin = '';
            this.error = false;
            this.message = '';
        },

        resetToWelcome() {
            this.pin = '';
            this.screen = 'welcome';
            this.state = null;
            this.userName = '';
            this.userAvatar = null;
            this.userInitials = '';
            this.message = '';
            this.statusMessage = '';
            this.error = false;
            this.hoursToday = null;
            this.breakEndsAt = null;
            this.$nextTick(() => this.$refs.pinInput?.focus());
        },

        setUser(user) {
            this.userName = user?.name || '';
            this.userAvatar = user?.avatar_url || null;
            this.userInitials = (this.userName || '?').split(' ').map((p) => p[0]).join('').slice(0, 2).toUpperCase();
        },

        stateMessage(data) {
            if (data.state === 'auto_checked_in') return 'You have been clocked in automatically.';
            if (data.state === 'checked_in') return 'You are currently clocked in.\nChoose an action below.';
            if (data.state === 'on_break') {
                const ends = data.break_ends_at
                    ? new Date(data.break_ends_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                    : '';
                return ends ? `You are on break until ${ends}.` : 'You are on break.';
            }
            if (data.state === 'not_checked_in') return 'You are not clocked in yet.';
            return 'Choose an action below.';
        },

        async verify() {
            if (this.pin.length !== 4 || this.loading) return;
            this.loading = true;
            this.error = false;
            this.message = '';
            try {
                const res = await fetch(this.verifyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ pin: this.pin }),
                });
                const data = await readJsonResponse(res);
                if (!res.ok) throw data;
                this.state = data.state;
                this.setUser(data.user);
                this.hoursToday = data.hours;
                this.breakEndsAt = data.break_ends_at || null;
                this.statusMessage = this.stateMessage(data);

                if (data.state === 'auto_checked_in') {
                    this.showSuccess('Welcome ' + this.userName.split(' ')[0] + '!', 'Clocked In Successfully', 'clock-in');
                    return;
                }

                this.screen = 'actions';
            } catch (e) {
                this.error = true;
                this.message = e?.errors?.pin?.[0] || e?.message || 'Wrong PIN — please try again.';
                this.pin = '';
                setTimeout(() => { this.message = ''; this.error = false; }, 2500);
            } finally {
                this.loading = false;
            }
        },

        async act(action) {
            if (this.pin.length !== 4 || this.loading) return;
            this.loading = true;
            this.error = false;
            try {
                const res = await fetch(this.actionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ pin: this.pin, action }),
                });
                const data = await readJsonResponse(res);
                if (!res.ok) throw data;
                this.state = data.state;
                this.setUser(data.user);
                this.hoursToday = data.hours;
                this.breakEndsAt = data.break_ends_at || null;

                const labels = {
                    'clock-in': ['Welcome ' + this.userName.split(' ')[0] + '!', 'Clocked In Successfully'],
                    'clock-out': ['Goodbye ' + this.userName.split(' ')[0] + '!', 'Clocked Out Successfully'],
                    'start-break': ['Break started', 'Enjoy your break'],
                    'end-break': ['Welcome back!', 'Break ended'],
                };
                const [title, subtitle] = labels[action] || ['Done', 'Updated successfully'];
                this.showSuccess(title, subtitle + (data.hours != null && action === 'clock-out' ? ' · ' + data.hours + ' hrs' : ''), action);
            } catch (e) {
                this.error = true;
                this.statusMessage = e?.errors?.action?.[0] || e?.errors?.pin?.[0] || e?.message || 'Action failed.';
            } finally {
                this.loading = false;
            }
        },

        showSuccess(title, subtitle, action) {
            this.successTitle = title;
            this.successSubtitle = subtitle;
            this.successTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            this.screen = 'success';
            const delay = (action === 'clock-out' || action === 'clock-in' || action === 'auto_checked_in')
                ? this.successSeconds * 1000
                : 2200;
            setTimeout(() => this.resetToWelcome(), delay);
        },

        showTodayHours() {
            const msg = this.hoursToday != null
                ? `Hours worked today: ${this.hoursToday}`
                : 'No completed hours recorded yet today.';
            this.statusMessage = msg;
        },

        async exitKiosk() {
            if (this.exitLoading) return;
            this.exitLoading = true;
            this.exitError = '';
            try {
                const res = await fetch(this.exitUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ password: this.exitPassword }),
                });
                const data = await readJsonResponse(res);
                if (!res.ok) throw data;
                window.location.href = '/business-admin/kiosk/settings';
            } catch (e) {
                this.exitError = e?.errors?.password?.[0] || e?.message || 'Incorrect password.';
            } finally {
                this.exitLoading = false;
            }
        },
    }));

    Alpine.data('smartKioskTerminal', (config = {}) => ({
        sessionActive: config.sessionActive === true,
        sessionAdminEmail: config.sessionAdminEmail || '',
        screen: 'home',
        pin: '',
        message: '',
        error: false,
        loading: false,
        clockTime: '',
        clockDate: '',
        clockTimer: null,
        startUrl: config.startUrl,
        pinUrl: config.pinUrl,
        actionUrl: config.actionUrl,
        exitUrl: config.exitUrl,
        csrf: config.csrf,
        branchName: config.branchName || '',
        kioskName: config.kioskName || 'Smart Kiosk',
        welcomeMessage: config.welcomeMessage || '',
        showPhotos: config.showPhotos !== false,
        logoUrl: config.logoUrl,
        adminEmail: '',
        adminPassword: '',
        startError: '',
        exitError: '',
        showExit: false,
        showExitEmail: false,
        logoHoldProgress: 0,
        logoHoldTimer: null,
        successUser: null,
        successLabel: '',
        successTime: '',
        successInitials: '',
        currentUser: null,
        currentPin: '',
        actionChoices: [],
        actionMessage: '',
        rotaMessage: '',

        init() {
            this.tickClock();
            this.clockTimer = setInterval(() => this.tickClock(), 1000);
        },

        tickClock() {
            const now = new Date();
            this.clockTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            this.clockDate = now.toLocaleDateString([], { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        },

        press(digit) {
            if (this.loading || this.pin.length >= 4) return;
            this.pin += String(digit);
            this.error = false;
            this.message = '';
            if (this.pin.length === 4) this.submitPin();
        },

        backspace() {
            this.pin = this.pin.slice(0, -1);
            this.error = false;
            this.message = '';
        },

        clearPin() {
            this.pin = '';
            this.error = false;
            this.message = '';
        },

        resetHome() {
            this.pin = '';
            this.currentPin = '';
            this.screen = 'home';
            this.message = '';
            this.error = false;
            this.successUser = null;
            this.currentUser = null;
            this.actionChoices = [];
            this.actionMessage = '';
            this.rotaMessage = '';
        },

        beginLogoHold() {
            if (!this.sessionActive) return;
            this.cancelLogoHold();
            this.logoHoldProgress = 0;
            this.logoHoldTimer = setInterval(() => {
                this.logoHoldProgress += 1;
                if (this.logoHoldProgress >= 100) {
                    this.cancelLogoHold();
                    this.openExitModal();
                }
            }, 50);
        },

        cancelLogoHold() {
            if (this.logoHoldTimer) {
                clearInterval(this.logoHoldTimer);
                this.logoHoldTimer = null;
            }
            this.logoHoldProgress = 0;
        },

        openExitModal() {
            this.exitError = '';
            this.adminPassword = '';
            this.adminEmail = this.sessionAdminEmail || '';
            this.showExitEmail = false;
            this.showExit = true;
        },

        async startKiosk() {
            this.loading = true;
            this.startError = '';
            try {
                const res = await fetch(this.startUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ email: this.adminEmail, password: this.adminPassword }),
                });
                const data = await readJsonResponse(res);
                if (!res.ok) throw data;
                this.sessionActive = true;
                this.sessionAdminEmail = this.adminEmail;
                this.adminPassword = '';
            } catch (e) {
                this.startError = e?.errors?.email?.[0] || e?.message || 'Unable to start kiosk.';
            } finally {
                this.loading = false;
            }
        },

        async submitPin() {
            if (this.pin.length !== 4 || this.loading) return;
            this.loading = true;
            this.error = false;
            this.message = '';
            this.currentPin = this.pin;
            try {
                const res = await fetch(this.pinUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ pin: this.pin }),
                });
                const data = await readJsonResponse(res);
                if (!res.ok) throw data;

                if (data.step === 'choose_action') {
                    this.currentUser = data.user;
                    this.actionChoices = data.actions || [];
                    this.actionMessage = data.message || 'What would you like to do?';
                    this.screen = 'choose';
                    this.pin = '';
                    return;
                }

                if (data.step === 'rota_restricted') {
                    this.currentUser = data.user;
                    this.rotaMessage = data.rota?.message || 'You are outside your scheduled clock-in window.';
                    this.screen = 'rota';
                    this.pin = '';
                    return;
                }

                this.showSuccess(data);
            } catch (e) {
                this.error = true;
                this.message = e?.errors?.pin?.[0] || e?.errors?.action?.[0] || e?.message || 'Wrong PIN — try again.';
                this.pin = '';
                setTimeout(() => { this.message = ''; this.error = false; }, 2500);
            } finally {
                this.loading = false;
            }
        },

        async performAction(item) {
            if (this.loading || !this.currentPin) return;
            this.loading = true;
            try {
                const res = await fetch(this.actionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        pin: this.currentPin,
                        action: item.action,
                        break_type: item.break_type || null,
                    }),
                });
                const data = await readJsonResponse(res);
                if (!res.ok) throw data;
                this.showSuccess(data);
            } catch (e) {
                this.error = true;
                this.message = e?.errors?.action?.[0] || e?.message || 'Unable to complete action.';
                this.screen = 'home';
                setTimeout(() => { this.message = ''; this.error = false; }, 2500);
            } finally {
                this.loading = false;
            }
        },

        showSuccess(data) {
            this.successUser = data.user;
            this.successInitials = (data.user?.name || '?').split(' ').map((p) => p[0]).join('').slice(0, 2).toUpperCase();
            this.successLabel = data.action_label;
            this.successTime = data.time;
            this.screen = 'success';
            this.pin = '';
            this.currentPin = '';
            setTimeout(() => this.resetHome(), 4000);
        },

        async exitKiosk() {
            this.loading = true;
            this.exitError = '';
            try {
                const payload = { password: this.adminPassword };
                if (this.showExitEmail && this.adminEmail) {
                    payload.email = this.adminEmail;
                }
                const res = await fetch(this.exitUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await readJsonResponse(res);
                if (!res.ok) throw data;
                this.sessionActive = false;
                this.showExit = false;
                this.adminPassword = '';
                this.resetHome();
            } catch (e) {
                this.exitError = e?.errors?.password?.[0] || e?.errors?.email?.[0] || e?.message || 'Unable to close kiosk.';
            } finally {
                this.loading = false;
            }
        },
    }));

    Alpine.data('kioskV2Terminal', (config = {}) => ({
        sessionActive: config.sessionActive === true,
        needsBranch: config.needsBranch === true,
        sessionAdminEmail: config.sessionAdminEmail || '',
        screen: 'home',
        pin: '',
        message: '',
        error: false,
        loading: false,
        clockTime: '',
        clockDate: '',
        clockTimer: null,
        loginUrl: config.loginUrl,
        selectBranchUrl: config.selectBranchUrl,
        pinUrl: config.pinUrl,
        actionUrl: config.actionUrl,
        logoutUrl: config.logoutUrl,
        csrf: config.csrf,
        branchName: config.branchName || '',
        displayName: config.displayName || 'Staff Clock',
        logoUrl: config.logoUrl,
        adminEmail: '',
        adminPassword: '',
        loginError: '',
        branches: config.branches || [],
        selectedBranchId: config.selectedBranchId || '',
        showAttendance: config.showAttendance === true,
        attendance: config.attendance || [],
        showAdmin: false,
        successUser: null,
        successLabel: '',
        successTime: '',
        successDetail: '',
        currentUser: null,
        currentPin: '',
        actionChoices: [],
        breakOptions: [],
        actionMessage: '',

        init() {
            this.tickClock();
            this.clockTimer = setInterval(() => this.tickClock(), 1000);
            if (this.needsBranch) {
                this.screen = 'branch';
            }
        },

        tickClock() {
            const now = new Date();
            this.clockTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            this.clockDate = now.toLocaleDateString([], { weekday: 'short', day: 'numeric', month: 'short' });
        },

        press(digit) {
            if (this.loading || this.pin.length >= 4) return;
            this.pin += String(digit);
            this.error = false;
            this.message = '';
            if (this.pin.length === 4) this.submitPin();
        },

        backspace() {
            this.pin = this.pin.slice(0, -1);
            this.error = false;
        },

        resetHome() {
            this.pin = '';
            this.currentPin = '';
            this.screen = 'home';
            this.message = '';
            this.error = false;
            this.successUser = null;
            this.currentUser = null;
            this.actionChoices = [];
            this.breakOptions = [];
            this.actionMessage = '';
        },

        unwrap(data) {
            return data?.data ?? data;
        },

        async login() {
            this.loading = true;
            this.loginError = '';
            try {
                const res = await fetch(this.loginUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ email: this.adminEmail, password: this.adminPassword }),
                });
                const data = await readJsonResponse(res);
                if (!res.ok) throw data;
                this.sessionAdminEmail = this.adminEmail;
                this.adminPassword = '';
                this.screen = 'branch';
                this.needsBranch = true;
            } catch (e) {
                this.loginError = e?.errors?.email?.[0] || e?.message || 'Unable to log in.';
            } finally {
                this.loading = false;
            }
        },

        async selectBranch() {
            if (!this.selectedBranchId) return;
            this.loading = true;
            try {
                const res = await fetch(this.selectBranchUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ branch_id: Number(this.selectedBranchId) }),
                });
                const data = this.unwrap(await readJsonResponse(res));
                if (!res.ok) throw data;
                this.sessionActive = true;
                this.needsBranch = false;
                this.branchName = data.branch?.name || this.branchName;
                this.attendance = data.attendance || [];
                this.screen = 'home';
            } catch (e) {
                this.loginError = e?.errors?.branch_id?.[0] || e?.message || 'Unable to select branch.';
            } finally {
                this.loading = false;
            }
        },

        async submitPin() {
            if (this.pin.length !== 4 || this.loading) return;
            this.loading = true;
            this.error = false;
            this.currentPin = this.pin;
            try {
                const res = await fetch(this.pinUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ pin: this.pin }),
                });
                const payload = await readJsonResponse(res);
                const data = this.unwrap(payload);
                if (!res.ok) throw payload;

                if (data.step === 'choose_action' || data.step === 'on_break') {
                    this.currentUser = data.user;
                    this.actionChoices = data.actions || [];
                    this.breakOptions = data.break_options || [];
                    this.actionMessage = data.message || 'What would you like to do?';
                    this.screen = data.step === 'on_break' ? 'on_break' : 'choose';
                    this.pin = '';
                    return;
                }

                this.showSuccess(data);
            } catch (e) {
                this.error = true;
                this.message = e?.errors?.pin?.[0] || e?.message || 'Invalid PIN.';
                this.pin = '';
                setTimeout(() => { this.message = ''; this.error = false; }, 2500);
            } finally {
                this.loading = false;
            }
        },

        async performAction(item) {
            if (this.loading || !this.currentPin) return;

            if (item.action === 'choose-break') {
                this.screen = 'breaks';
                this.breakOptions = this.breakOptions.length ? this.breakOptions : [];
                return;
            }

            this.loading = true;
            try {
                const res = await fetch(this.actionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        pin: this.currentPin,
                        action: item.action,
                        break_type: item.break_type || item.value || null,
                    }),
                });
                const payload = await readJsonResponse(res);
                const data = this.unwrap(payload);
                if (!res.ok) throw payload;

                if (data.step === 'choose_break') {
                    this.breakOptions = data.break_options || [];
                    this.screen = 'breaks';
                    return;
                }

                this.showSuccess(data);
            } catch (e) {
                this.error = true;
                this.message = e?.errors?.action?.[0] || e?.message || 'Unable to complete action.';
                this.screen = 'home';
            } finally {
                this.loading = false;
            }
        },

        showSuccess(data) {
            this.successUser = data.user;
            this.successLabel = data.action_label;
            this.successTime = data.time;
            this.successDetail = data.net_label || '';
            this.screen = 'success';
            this.pin = '';
            this.currentPin = '';
            setTimeout(() => this.resetHome(), 3000);
        },

        async logoutKiosk() {
            this.loading = true;
            try {
                await fetch(this.logoutUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken(this.csrf),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                window.location.reload();
            } finally {
                this.loading = false;
            }
        },
    }));

    Alpine.data('reportLineChart', (series = [], format = 'currency') => ({
        points: [],
        hoverLabel: '',
        linePath: '',
        areaPath: '',
        init() {
            const values = (series || []).map((point) => Number(point.value || 0));
            const max = Math.max(...values, 1);
            const count = Math.max(values.length - 1, 1);

            this.points = (series || []).map((point, index) => ({
                ...point,
                x: (index / count) * 100,
                y: 38 - ((Number(point.value || 0) / max) * 34),
            }));

            this.linePath = this.points.map((point, index) => `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`).join(' ');
            this.areaPath = `${this.linePath} L 100 40 L 0 40 Z`;
        },
        setHover(index) {
            const point = this.points[index];
            if (! point) {
                return;
            }

            const value = Number(point.value || 0);
            this.hoverLabel = format === 'currency'
                ? `${point.label}: £${value.toFixed(2)}`
                : `${point.label}: ${value}`;
        },
        clearHover() {
            this.hoverLabel = '';
        },
    }));

    Alpine.data('reportTable', (columns = [], rows = []) => ({
        columns,
        rows,
        search: '',
        sortColumn: null,
        sortDirection: 'asc',
        page: 1,
        pageSize: 15,
        get filteredRows() {
            if (! this.search.trim()) {
                return this.rows;
            }

            const term = this.search.toLowerCase();

            return this.rows.filter((row) => row.some((cell) => String(cell).toLowerCase().includes(term)));
        },
        get sortedRows() {
            if (this.sortColumn === null) {
                return this.filteredRows;
            }

            const column = this.sortColumn;

            return [...this.filteredRows].sort((a, b) => {
                const left = String(a[column] ?? '');
                const right = String(b[column] ?? '');
                const result = left.localeCompare(right, undefined, { numeric: true, sensitivity: 'base' });

                return this.sortDirection === 'asc' ? result : -result;
            });
        },
        get totalPages() {
            return Math.max(1, Math.ceil(this.sortedRows.length / this.pageSize));
        },
        get paginatedRows() {
            const start = (this.page - 1) * this.pageSize;

            return this.sortedRows.slice(start, start + this.pageSize);
        },
        sortBy(index) {
            if (this.sortColumn === index) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = index;
                this.sortDirection = 'asc';
            }
        },
        prevPage() {
            this.page = Math.max(1, this.page - 1);
        },
        nextPage() {
            this.page = Math.min(this.totalPages, this.page + 1);
        },
    }));

    Alpine.data('rotaBoard', (config = {}) => ({
        tab: 'weekly',
        weekStart: config.weekStart,
        days: config.days || [],
        staff: config.staff || [],
        sections: config.sections || [],
        groups: config.groups || [],
        morningGrid: config.morningGrid || [],
        eveningGrid: config.eveningGrid || [],
        shiftForm: {
            id: null,
            user_id: '',
            shift_date: '',
            shift_type: 'Morning',
            rota_section_id: '',
            start_time: '09:00',
            end_time: '17:00',
        },
        sectionForm: { name: '', color: '#563d7c' },
        groupForm: { name: '', color: '#007bff', display_order: 0, user_ids: [] },

        calcHours() {
            if (! this.shiftForm.start_time || ! this.shiftForm.end_time) {
                return '0.0';
            }
            const [sh, sm] = this.shiftForm.start_time.split(':').map(Number);
            const [eh, em] = this.shiftForm.end_time.split(':').map(Number);
            let mins = (eh * 60 + em) - (sh * 60 + sm);
            if (mins <= 0) {
                mins += 24 * 60;
            }
            return (mins / 60).toFixed(1);
        },

        isOvernight() {
            if (! this.shiftForm.start_time || ! this.shiftForm.end_time) {
                return false;
            }
            return this.shiftForm.end_time <= this.shiftForm.start_time;
        },

        openShift(staffId, date, type, existing = null) {
            const staff = this.staff.find((s) => s.id === staffId);
            this.shiftForm = {
                id: existing?.id || null,
                user_id: staffId,
                user_name: staff?.name || '',
                shift_date: date,
                shift_type: type,
                rota_section_id: existing?.rota_section_id || (this.sections[0]?.id || ''),
                start_time: existing?.start_time || (type === 'Morning' ? '09:00' : '17:00'),
                end_time: existing?.end_time || (type === 'Morning' ? '17:00' : '23:00'),
            };
            this.$dispatch('open-modal', 'shift-modal');
        },

        cellLabel(shift) {
            if (! shift) {
                return '';
            }
            return `${shift.start_time}-${shift.end_time}`;
        },

        cellStyle(shift) {
            if (! shift?.color) {
                return '';
            }
            return `background:${shift.color}22;border-color:${shift.color};color:${shift.color}`;
        },

        prevWeek() {
            const d = new Date(this.weekStart + 'T00:00:00');
            d.setDate(d.getDate() - 7);
            window.location = `?week=${d.toISOString().slice(0, 10)}`;
        },

        nextWeek() {
            const d = new Date(this.weekStart + 'T00:00:00');
            d.setDate(d.getDate() + 7);
            window.location = `?week=${d.toISOString().slice(0, 10)}`;
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

if (window.location.pathname.startsWith('/staff')) {
    registerStaffPwa();
}
