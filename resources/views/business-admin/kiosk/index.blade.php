<x-layouts.kiosk
    title="Attendance Kiosk"
    :organization-name="$organization?->name"
>
    <div
        class="kiosk-terminal relative flex min-h-screen flex-col"
        x-data="attendanceKiosk(@js([
            'verifyUrl' => route('business-admin.kiosk.verify'),
            'actionUrl' => route('business-admin.kiosk.action'),
            'exitUrl' => route('business-admin.kiosk.exit'),
            'csrf' => csrf_token(),
            'branchName' => $branch->name,
            'organizationName' => $organization?->name,
            'welcomeMessage' => $settings['welcome_message'],
            'successSeconds' => $settings['success_display_seconds'],
            'showPhotos' => $settings['show_photos'],
            'logoUrl' => $logoUrl,
        ]))"
        x-init="init()"
        @keydown.window.prevent.ctrl.shift.k="showExit = true"
    >
        {{-- Header --}}
        <header class="kiosk-header flex shrink-0 items-center justify-between gap-4 border-b border-emerald-200/60 bg-white/80 px-5 py-4 backdrop-blur-md dark:border-emerald-900/40 dark:bg-gray-900/80 sm:px-8">
            <div class="flex min-w-0 items-center gap-4">
                <img src="{{ $logoUrl }}" alt="{{ $organization?->name ?? brand_name() }}" class="h-12 w-12 shrink-0 rounded-2xl border border-emerald-100 bg-white object-contain p-1 shadow-sm dark:border-emerald-900 dark:bg-gray-800 sm:h-14 sm:w-14">
                <div class="min-w-0">
                    <p class="truncate font-display text-lg font-bold text-gray-900 dark:text-white sm:text-xl">{{ $organization?->name }}</p>
                    <p class="truncate text-sm font-medium text-emerald-700 dark:text-emerald-400">{{ $branch->name }}</p>
                </div>
            </div>

            <div class="text-right">
                <p class="font-display text-2xl font-bold tabular-nums tracking-tight text-gray-900 dark:text-white sm:text-3xl" x-text="clockTime"></p>
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500 dark:text-gray-400" x-text="clockDate"></p>
            </div>
        </header>

        {{-- Main --}}
        <main class="flex flex-1 flex-col items-center justify-center px-4 py-6 sm:px-8 sm:py-10">
            {{-- Welcome / PIN screen --}}
            <div
                x-show="screen === 'welcome'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="w-full max-w-lg"
            >
                <div class="text-center">
                    <h1 class="font-display text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">Attendance Kiosk</h1>
                    <p class="mt-3 text-base text-gray-600 dark:text-gray-300" x-text="welcomeMessage"></p>
                </div>

                <div class="mt-8 flex justify-center gap-3">
                    <template x-for="(_, index) in 4" :key="index">
                        <div
                            class="flex h-16 w-14 items-center justify-center rounded-2xl border-2 text-3xl font-bold transition-all duration-200 sm:h-[4.5rem] sm:w-16"
                            :class="pin.length > index
                                ? 'border-emerald-500 bg-emerald-500 text-white shadow-lg shadow-emerald-500/25 scale-105'
                                : 'border-gray-200 bg-white text-transparent dark:border-gray-700 dark:bg-gray-800'"
                        >
                            <span x-show="pin.length > index">•</span>
                        </div>
                    </template>
                </div>

                <p
                    class="mt-4 min-h-[1.5rem] text-center text-sm font-semibold transition-all"
                    :class="error ? 'text-red-600 animate-kiosk-shake' : 'text-emerald-700 dark:text-emerald-400'"
                    x-text="message"
                    x-show="message"
                ></p>

                <div class="mt-6 grid grid-cols-3 gap-3 sm:gap-4">
                    <template x-for="digit in ['1','2','3','4','5','6','7','8','9']" :key="digit">
                        <button
                            type="button"
                            class="kiosk-keypad-btn"
                            @click="press(digit)"
                            :disabled="loading"
                            x-text="digit"
                        ></button>
                    </template>
                    <button type="button" class="kiosk-keypad-btn kiosk-keypad-btn-muted" @click="clearPin()" :disabled="loading">Clear</button>
                    <button type="button" class="kiosk-keypad-btn" @click="press('0')" :disabled="loading">0</button>
                    <button type="button" class="kiosk-keypad-btn kiosk-keypad-btn-danger" @click="backspace()" :disabled="loading" aria-label="Delete">⌫</button>
                </div>

                <input
                    type="password"
                    inputmode="numeric"
                    maxlength="4"
                    autocomplete="one-time-code"
                    class="sr-only"
                    x-ref="pinInput"
                    x-model="pin"
                    @input="pin = String(pin).replace(/\D/g, '').slice(0, 4); if (pin.length === 4) verify()"
                >
            </div>

            {{-- Actions screen --}}
            <div
                x-show="screen === 'actions'"
                x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="w-full max-w-md text-center"
            >
                <div class="mx-auto mb-5 flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border-4 border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950/50">
                    <template x-if="showPhotos && userAvatar">
                        <img :src="userAvatar" :alt="userName" class="h-full w-full object-cover">
                    </template>
                    <template x-if="!showPhotos || !userAvatar">
                        <span class="font-display text-3xl font-bold text-emerald-700 dark:text-emerald-300" x-text="userInitials"></span>
                    </template>
                </div>

                <h2 class="font-display text-2xl font-bold text-gray-900 dark:text-white" x-text="'Hi, ' + userName + '!'"></h2>
                <p class="mt-2 whitespace-pre-line text-sm text-gray-600 dark:text-gray-300" x-text="statusMessage"></p>
                <p class="mt-1 text-sm font-semibold text-emerald-700 dark:text-emerald-400" x-show="hoursToday !== null" x-text="'Hours today: ' + hoursToday"></p>

                <div class="mt-8 grid gap-3">
                    <button type="button" class="kiosk-action-btn kiosk-action-btn-primary" x-show="state === 'not_checked_in'" @click="act('clock-in')" :disabled="loading">Clock In</button>
                    <button type="button" class="kiosk-action-btn" x-show="state === 'checked_in' || state === 'auto_checked_in'" @click="act('clock-out')" :disabled="loading">Clock Out</button>
                    <button type="button" class="kiosk-action-btn" x-show="state === 'checked_in' || state === 'auto_checked_in'" @click="act('start-break')" :disabled="loading">Start Break</button>
                    <button type="button" class="kiosk-action-btn" x-show="state === 'on_break'" @click="act('end-break')" :disabled="loading">End Break</button>
                    <button type="button" class="kiosk-action-btn kiosk-action-btn-muted" @click="showTodayHours()" :disabled="loading">View Today&apos;s Hours</button>
                </div>

                <button type="button" class="mt-6 text-sm font-semibold text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" @click="resetToWelcome()">Cancel</button>
            </div>

            {{-- Success screen --}}
            <div
                x-show="screen === 'success'"
                x-cloak
                x-transition:enter="transition ease-out duration-400"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                class="w-full max-w-md text-center"
            >
                <div class="mx-auto mb-6 flex h-28 w-28 items-center justify-center rounded-full bg-emerald-500 text-white shadow-xl shadow-emerald-500/30">
                    <svg class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="font-display text-3xl font-extrabold text-gray-900 dark:text-white" x-text="successTitle"></h2>
                <p class="mt-2 text-lg font-semibold text-emerald-700 dark:text-emerald-400" x-text="successSubtitle"></p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="successTime"></p>
                <p class="mt-1 text-sm font-medium text-gray-600 dark:text-gray-300" x-text="branchName"></p>
            </div>
        </main>

        {{-- Footer hint --}}
        <footer class="shrink-0 px-5 py-3 text-center text-[11px] font-medium uppercase tracking-[0.16em] text-gray-400 dark:text-gray-500">
            {{ brand_name() }} · Attendance Kiosk
        </footer>

        {{-- Hidden exit trigger --}}
        <button
            type="button"
            class="fixed bottom-3 left-3 h-10 w-10 rounded-full opacity-0"
            aria-label="Exit kiosk"
            @click="showExit = true"
        ></button>

        {{-- Exit modal --}}
        <div
            x-show="showExit"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm"
            @keydown.escape.window="showExit = false"
        >
            <div class="w-full max-w-sm rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl dark:border-gray-700 dark:bg-gray-900" @click.outside="showExit = false">
                <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Exit Kiosk Mode</h3>
                <p class="mt-2 text-sm text-gray-500">Enter your admin password to leave kiosk mode.</p>
                <form class="mt-4 space-y-3" @submit.prevent="exitKiosk()">
                    <input type="password" x-model="exitPassword" class="admin-input w-full" placeholder="Admin password" autocomplete="current-password" required>
                    <p class="text-sm text-red-600" x-show="exitError" x-text="exitError"></p>
                    <div class="flex gap-2">
                        <button type="button" class="admin-btn-secondary flex-1" @click="showExit = false">Cancel</button>
                        <button type="submit" class="admin-btn-primary flex-1" :disabled="exitLoading">Exit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.kiosk>
