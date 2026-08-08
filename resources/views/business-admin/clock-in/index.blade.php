<x-layouts.business-admin title="Clock In" active="clock-in">
    <div
        class="mx-auto w-full max-w-md px-1 sm:max-w-[28rem]"
        x-data="clockKiosk({
            verifyUrl: @js(route('business-admin.clock-in.verify')),
            actionUrl: @js(route('business-admin.clock-in.action')),
            csrf: @js(csrf_token()),
        })"
    >
        <x-admin.card class="text-center">
            <div x-show="screen === 'pin'">
                <h3 class="font-display text-2xl font-bold text-gray-900 dark:text-white">Staff Clock-In</h3>
                <p class="mt-2 text-sm text-gray-500">Please enter your security PIN code</p>

                <div class="admin-pin-display mt-6" role="status" aria-live="polite" aria-label="PIN entry progress">
                    <template x-for="i in 4" :key="i">
                        <div
                            class="admin-pin-dot"
                            :class="pin.length >= i ? 'admin-pin-dot-filled' : 'admin-pin-dot-empty'"
                            x-text="pin.length >= i ? '•' : ''"
                        ></div>
                    </template>
                </div>

                <p class="mt-3 min-h-[1.25rem] text-sm" :class="error ? 'text-red-600' : 'text-primary-700'" x-text="message"></p>

                <div class="admin-keypad-grid mt-4">
                    @foreach (['1', '2', '3', '4', '5', '6', '7', '8', '9'] as $digit)
                        <button type="button" class="admin-keypad-key" @click="press('{{ $digit }}')" aria-label="Digit {{ $digit }}">{{ $digit }}</button>
                    @endforeach
                    <div aria-hidden="true"></div>
                    <button type="button" class="admin-keypad-key" @click="press('0')" aria-label="Digit 0">0</button>
                    <button type="button" class="admin-keypad-key admin-keypad-key--danger" @click="backspace()" aria-label="Backspace">⌫</button>
                </div>

                <div class="mt-4">
                    <label class="sr-only" for="pin-type">PIN</label>
                    <input
                        id="pin-type"
                        type="password"
                        inputmode="numeric"
                        maxlength="4"
                        pattern="[0-9]*"
                        autocomplete="one-time-code"
                        class="admin-input min-h-[48px] text-center tracking-[0.35em]"
                        placeholder="Or type PIN"
                        x-model="pin"
                        @input="pin = String(pin).replace(/\D/g, '').slice(0, 4); if (pin.length === 4) verify()"
                    >
                </div>
            </div>

            <div x-show="screen === 'action'" x-cloak class="relative py-2">
                <button type="button" class="admin-touch-target absolute right-0 top-0 rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800" @click="resetToPin()" aria-label="Close">✕</button>

                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-3xl text-primary-600 dark:bg-primary-900/30" aria-hidden="true">✓</div>
                <h4 class="font-display text-xl font-bold text-gray-900 dark:text-white" x-text="'Hi! ' + userName"></h4>
                <p class="mt-2 whitespace-pre-line text-sm text-gray-500" x-text="message"></p>

                <div class="mt-6">
                    <x-admin.action-grid>
                        <button type="button" class="admin-action-tile" x-show="state === 'not_checked_in'" @click="act('clock-in')" :disabled="loading">
                            <x-admin.icon name="clock" class="admin-action-tile__icon" />
                            <span class="admin-action-tile__label">Clock In</span>
                        </button>
                        <button type="button" class="admin-action-tile" x-show="state === 'checked_in' || state === 'auto_checked_in'" @click="act('clock-out')" :disabled="loading">
                            <x-admin.icon name="logout" class="admin-action-tile__icon" />
                            <span class="admin-action-tile__label">Clock Out</span>
                        </button>
                        <button type="button" class="admin-action-tile" x-show="state === 'checked_in' || state === 'auto_checked_in'" @click="act('start-break')" :disabled="loading">
                            <x-admin.icon name="pause" class="admin-action-tile__icon" />
                            <span class="admin-action-tile__label">Start Break</span>
                        </button>
                        <button type="button" class="admin-action-tile" x-show="state === 'on_break'" @click="act('end-break')" :disabled="loading">
                            <x-admin.icon name="check" class="admin-action-tile__icon" />
                            <span class="admin-action-tile__label">End Break</span>
                        </button>
                    </x-admin.action-grid>
                </div>

                <button type="button" class="admin-touch-target mt-3 min-h-[44px] text-sm font-semibold text-gray-500" @click="resetToPin()">Cancel</button>
            </div>
        </x-admin.card>
    </div>
</x-layouts.business-admin>
