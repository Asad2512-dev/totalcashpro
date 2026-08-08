<x-layouts.kiosk
    title="Smart Attendance Kiosk"
    :organization-name="$organization?->name"
>
    <div
        class="relative min-h-screen"
        x-data="smartKioskTerminal(@js([
            'sessionActive' => $sessionActive,
            'sessionAdminEmail' => $sessionAdminEmail,
            'startUrl' => route('kiosk.start', $kiosk->token),
            'pinUrl' => route('kiosk.pin', $kiosk->token),
            'actionUrl' => route('kiosk.action', $kiosk->token),
            'exitUrl' => route('kiosk.exit', $kiosk->token),
            'csrf' => csrf_token(),
            'branchName' => $branch->name,
            'kioskName' => $kiosk->name,
            'welcomeMessage' => $kiosk->welcome_message,
            'showPhotos' => $kiosk->show_photos,
            'logoUrl' => $logoUrl,
        ]))"
        x-init="init()"
    >
        {{-- Admin start screen — matches main login layout --}}
        <div x-show="!sessionActive" x-cloak class="grid min-h-screen lg:grid-cols-2">
            <section class="relative hidden overflow-hidden auth-shell text-white lg:flex lg:flex-col lg:justify-between lg:p-12 xl:p-16">
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-3 drop-shadow-md">
                        <img src="{{ $logoUrl }}" alt="{{ $organization?->name ?? brand_name() }}" class="h-11 w-11 rounded-2xl border border-white/20 bg-white object-contain p-1 shadow-lg">
                        <div>
                            <p class="font-display text-lg font-bold leading-tight">{{ $organization?->name }}</p>
                            <p class="text-sm text-white/70">{{ $branch->name }}</p>
                        </div>
                    </div>
                    <h1 class="mt-14 max-w-md font-display text-4xl font-extrabold tracking-tight xl:text-5xl">
                        Start your smart attendance kiosk
                    </h1>
                    <p class="mt-5 max-w-md text-base leading-relaxed text-white/75">
                        Sign in once as a business admin. Staff then clock in and out with their 4-digit PIN — no further admin login required.
                    </p>
                </div>

                <div class="relative z-10 mt-12 space-y-4">
                    <div class="glass-panel rounded-3xl p-5">
                        <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-primary-200">{{ $kiosk->name }}</p>
                        <div class="mt-4 grid grid-cols-3 gap-3">
                            <div class="rounded-2xl bg-white/10 p-3">
                                <p class="text-xs text-white/60">Entry</p>
                                <p class="mt-1 font-display text-xl font-bold">PIN</p>
                            </div>
                            <div class="rounded-2xl bg-white/10 p-3">
                                <p class="text-xs text-white/60">Mode</p>
                                <p class="mt-1 font-display text-xl font-bold">Auto</p>
                            </div>
                            <div class="rounded-2xl bg-white/10 p-3">
                                <p class="text-xs text-white/60">Branch</p>
                                <p class="mt-1 truncate font-display text-lg font-bold">{{ $branch->name }}</p>
                            </div>
                        </div>
                    </div>
                    <ul class="space-y-3 text-sm text-white/80">
                        <li class="flex gap-2"><span class="text-primary-300">✓</span> Secure token URL — bookmark on your tablet</li>
                        <li class="flex gap-2"><span class="text-primary-300">✓</span> Smart clock in, clock out and break handling</li>
                        <li class="flex gap-2"><span class="text-primary-300">✓</span> Admin password required to close the session</li>
                    </ul>
                </div>
            </section>

            <section class="flex items-center justify-center bg-gray-50 px-6 py-12 sm:px-10 dark:bg-gray-950">
                <div class="w-full max-w-md">
                    <div class="mb-10 lg:hidden">
                        <div class="inline-flex items-center gap-3">
                            <img src="{{ $logoUrl }}" alt="{{ $organization?->name ?? brand_name() }}" class="h-11 w-11 rounded-2xl border border-gray-200 bg-white object-contain p-1 shadow-sm">
                            <div>
                                <p class="font-display text-lg font-bold text-gray-900 dark:text-white">{{ $organization?->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $branch->name }}</p>
                            </div>
                        </div>
                    </div>

                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-primary-600">Smart kiosk</p>
                    <h2 class="mt-3 font-display text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">Start kiosk session</h2>
                    <p class="mt-3 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                        Business admin sign-in required once. This session stays active on this device until you close the kiosk.
                    </p>

                    <form class="mt-8 space-y-5" @submit.prevent="startKiosk()">
                        <div>
                            <label for="start-email" class="text-sm font-semibold text-gray-900 dark:text-white">Admin email</label>
                            <input
                                id="start-email"
                                type="email"
                                x-model="adminEmail"
                                required
                                autocomplete="username"
                                class="mt-2 w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            >
                        </div>
                        <div>
                            <label for="start-password" class="text-sm font-semibold text-gray-900 dark:text-white">Password</label>
                            <div class="relative mt-2" x-data="{ showPassword: false }">
                                <input
                                    id="start-password"
                                    type="password"
                                    x-model="adminPassword"
                                    x-bind:type="showPassword ? 'text' : 'password'"
                                    required
                                    autocomplete="current-password"
                                    class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 pr-12 text-sm outline-none transition focus:border-primary-600 focus:ring-2 focus:ring-primary-600/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                >
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-300"
                                    @click="showPassword = !showPassword"
                                    x-bind:aria-label="showPassword ? 'Hide password' : 'Show password'"
                                >
                                    <svg x-show="!showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div
                            x-show="startError"
                            x-cloak
                            class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300"
                            x-text="startError"
                        ></div>

                        <button
                            type="submit"
                            class="btn-ripple inline-flex w-full min-h-[52px] items-center justify-center gap-2 rounded-2xl bg-primary-600 px-6 py-3.5 text-base font-semibold text-white transition hover:-translate-y-0.5 hover:bg-primary-700 disabled:pointer-events-none disabled:translate-y-0 disabled:opacity-80"
                            :disabled="loading"
                        >
                            <span x-show="loading" x-cloak class="inline-flex h-5 w-5 animate-spin rounded-full border-2 border-white/30 border-t-white" aria-hidden="true"></span>
                            <span x-text="loading ? 'Starting kiosk…' : 'Start Kiosk'"></span>
                        </button>
                    </form>

                    <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                        <p class="font-semibold text-gray-900 dark:text-white">After you start</p>
                        <p class="mt-1">Staff enter their 4-digit PIN to clock in or out. Use <span class="font-medium text-gray-700 dark:text-gray-300">Close Kiosk</span> and your admin password when the shift ends.</p>
                    </div>

                    <p class="mt-10 text-center text-xs text-gray-400">
                        © {{ date('Y') }} {{ brand_name() }} · Smart Attendance Kiosk
                    </p>
                </div>
            </section>
        </div>

        {{-- Active kiosk terminal --}}
        <div x-show="sessionActive" x-cloak class="kiosk-terminal flex min-h-screen flex-col bg-gradient-to-br from-emerald-50 via-white to-emerald-100 dark:from-gray-950 dark:via-gray-900 dark:to-emerald-950">
        <header class="kiosk-header flex shrink-0 items-center justify-between gap-4 border-b border-emerald-200/60 bg-white/80 px-5 py-4 backdrop-blur-md dark:border-emerald-900/40 dark:bg-gray-900/80 sm:px-8">
            <div
                class="flex min-w-0 items-center gap-4 select-none"
                @mousedown="beginLogoHold()"
                @mouseup="cancelLogoHold()"
                @mouseleave="cancelLogoHold()"
                @touchstart.prevent="beginLogoHold()"
                @touchend.prevent="cancelLogoHold()"
                @touchcancel.prevent="cancelLogoHold()"
            >
                <div class="relative">
                    <img src="{{ $logoUrl }}" alt="{{ $organization?->name ?? brand_name() }}" class="h-12 w-12 shrink-0 rounded-2xl border border-emerald-100 bg-white object-contain p-1 shadow-sm dark:border-emerald-900 dark:bg-gray-800 sm:h-14 sm:w-14">
                    <div
                        x-show="logoHoldProgress > 0"
                        x-cloak
                        class="absolute inset-0 flex items-center justify-center rounded-2xl bg-gray-900/70"
                    >
                        <span class="text-xs font-bold text-white" x-text="Math.ceil(5 - logoHoldProgress / 20) + 's'"></span>
                    </div>
                </div>
                <div class="min-w-0">
                    <p class="truncate font-display text-lg font-bold text-gray-900 dark:text-white sm:text-xl">{{ $organization?->name }}</p>
                    <p class="truncate text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ $branch->name }}</p>
                </div>
            </div>
            <div class="flex shrink-0 items-center gap-3">
                <div class="text-right">
                    <p class="font-display text-2xl font-bold tabular-nums tracking-tight text-gray-900 dark:text-white sm:text-3xl" x-text="clockTime"></p>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500 dark:text-gray-400" x-text="clockDate"></p>
                </div>
            </div>
        </header>

        <main class="flex flex-1 flex-col items-center justify-center px-4 py-6 sm:px-8 sm:py-10">
            {{-- PIN home --}}
            <div x-show="screen === 'home'" class="w-full max-w-lg">
                <div class="text-center">
                    <h1 class="font-display text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl" x-text="kioskName"></h1>
                    <p class="mt-3 text-base text-gray-600 dark:text-gray-300" x-text="welcomeMessage"></p>
                </div>

                <div class="mt-8 flex justify-center gap-3">
                    <template x-for="(_, index) in 4" :key="index">
                        <div
                            class="flex h-16 w-14 items-center justify-center rounded-2xl border-2 text-3xl font-bold transition-all duration-200 sm:h-[4.5rem] sm:w-16"
                            :class="pin.length > index
                                ? 'border-emerald-500 bg-emerald-500 text-white shadow-lg shadow-emerald-500/25'
                                : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800'"
                        >
                            <span x-show="pin.length > index">•</span>
                        </div>
                    </template>
                </div>

                <p
                    class="mt-4 min-h-[1.5rem] text-center text-sm font-semibold"
                    :class="error ? 'text-red-600 animate-kiosk-shake' : 'text-transparent'"
                    x-text="message"
                ></p>

                <div class="mt-6 grid grid-cols-3 gap-3 sm:gap-4">
                    <template x-for="digit in ['1','2','3','4','5','6','7','8','9']" :key="digit">
                        <button type="button" class="kiosk-keypad-btn" @click="press(digit)" :disabled="loading" x-text="digit"></button>
                    </template>
                    <button type="button" class="kiosk-keypad-btn kiosk-keypad-btn-muted" @click="clearPin()" :disabled="loading">Clear</button>
                    <button type="button" class="kiosk-keypad-btn" @click="press('0')" :disabled="loading">0</button>
                    <button type="button" class="kiosk-keypad-btn kiosk-keypad-btn-danger" @click="backspace()" :disabled="loading" aria-label="Delete">⌫</button>
                </div>
            </div>

            {{-- Action chooser --}}
            <div x-show="screen === 'choose'" x-cloak class="w-full max-w-lg">
                <div class="text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.14em] text-emerald-600 dark:text-emerald-400" x-text="'Hi, ' + (currentUser?.name?.split(' ')[0] || '')"></p>
                    <h2 class="mt-2 font-display text-2xl font-bold text-gray-900 dark:text-white" x-text="actionMessage"></h2>
                </div>
                <div class="mt-8 grid gap-3">
                    <template x-for="item in actionChoices" :key="item.action + (item.break_type || '')">
                        <button
                            type="button"
                            class="kiosk-action-btn"
                            :class="item.action === 'clock-out' ? 'kiosk-action-btn-danger' : ''"
                            @click="performAction(item)"
                            :disabled="loading"
                            x-text="item.label"
                        ></button>
                    </template>
                    <button type="button" class="kiosk-action-btn kiosk-action-btn-muted mt-2" @click="resetHome()" :disabled="loading">Cancel</button>
                </div>
            </div>

            {{-- Rota restricted --}}
            <div x-show="screen === 'rota'" x-cloak class="w-full max-w-lg text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                </div>
                <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white">Outside scheduled window</h2>
                <p class="mt-3 text-base text-gray-600 dark:text-gray-300" x-text="rotaMessage"></p>
                <div class="mt-8 grid gap-3">
                    <button type="button" class="kiosk-action-btn" @click="performAction({ action: 'clock-in-override' })" :disabled="loading">Admin Override</button>
                    <button type="button" class="kiosk-action-btn kiosk-action-btn-muted" @click="resetHome()" :disabled="loading">Cancel</button>
                </div>
            </div>

            {{-- Success --}}
            <div x-show="screen === 'success'" x-cloak class="w-full max-w-md text-center">
                <div class="mx-auto mb-5 flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border-4 border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950/50">
                    <template x-if="showPhotos && successUser?.avatar_url">
                        <img :src="successUser.avatar_url" :alt="successUser.name" class="h-full w-full object-cover">
                    </template>
                    <template x-if="!showPhotos || !successUser?.avatar_url">
                        <span class="font-display text-3xl font-bold text-emerald-700" x-text="successInitials"></span>
                    </template>
                </div>
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500 text-white shadow-xl shadow-emerald-500/30">
                    <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="font-display text-3xl font-extrabold text-gray-900 dark:text-white" x-text="'Welcome, ' + (successUser?.name?.split(' ')[0] || '') + '!'"></h2>
                <p class="mt-2 text-lg font-semibold text-emerald-700 dark:text-emerald-400" x-text="successLabel"></p>
                <p class="mt-1 text-sm text-gray-500" x-text="successTime"></p>
                <p class="mt-1 text-sm font-medium text-gray-600 dark:text-gray-300" x-text="branchName"></p>
            </div>
        </main>

        <footer class="shrink-0 px-5 py-3 text-center text-[11px] font-medium uppercase tracking-[0.16em] text-gray-400">
            {{ brand_name() }} · Smart Attendance Kiosk
        </footer>

        {{-- Exit modal --}}
        <div
            x-show="showExit"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/70 p-4 backdrop-blur-sm"
            @keydown.escape.window="showExit = false"
        >
            <div class="w-full max-w-sm rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl dark:border-gray-700 dark:bg-gray-900" @click.outside="showExit = false">
                <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Close Kiosk</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Enter the business admin password to end this kiosk session.</p>
                <form class="mt-4 space-y-3" @submit.prevent="exitKiosk()">
                    <div x-show="showExitEmail" x-cloak>
                        <label class="admin-label" for="exit-email">Admin email</label>
                        <input id="exit-email" type="email" x-model="adminEmail" class="admin-input mt-1 w-full" autocomplete="username">
                    </div>
                    <div>
                        <label class="admin-label" for="exit-password">Admin password</label>
                        <input id="exit-password" type="password" x-model="adminPassword" class="admin-input mt-1 w-full" required autocomplete="current-password" placeholder="Enter admin password">
                    </div>
                    <button
                        type="button"
                        class="text-xs font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400"
                        @click="showExitEmail = !showExitEmail"
                        x-text="showExitEmail ? 'Use session admin only' : 'Sign in as a different admin'"
                    ></button>
                    <p class="text-sm text-red-600" x-show="exitError" x-text="exitError"></p>
                    <div class="flex gap-2">
                        <button type="button" class="admin-btn-secondary flex-1" @click="showExit = false">Cancel</button>
                        <button type="submit" class="admin-btn-primary flex-1" :disabled="loading">Close Kiosk</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</x-layouts.kiosk>
