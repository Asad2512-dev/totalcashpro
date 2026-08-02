<x-layouts.business-admin title="Clock In" active="clock-in">
    <div
        class="mx-auto max-w-[450px]"
        x-data="clockKiosk({
            verifyUrl: @js(route('business-admin.clock-in.verify')),
            actionUrl: @js(route('business-admin.clock-in.action')),
            csrf: @js(csrf_token()),
        })"
    >
        <x-admin.card class="text-center">
            {{-- Screen 1: PIN --}}
            <div x-show="screen === 'pin'">
                <h3 class="font-display text-2xl font-bold text-gray-900 dark:text-white">Staff Clock-In</h3>
                <p class="mt-2 text-sm text-gray-500">Please enter your security PIN code</p>

                <div class="mt-6 flex justify-center gap-2">
                    <div class="flex h-[60px] w-[50px] items-center justify-center rounded-xl border text-2xl font-bold dark:border-gray-700" :class="pin.length >= 1 ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 bg-white dark:bg-gray-900'" x-text="pin.length >= 1 ? '•' : ''"></div>
                    <div class="flex h-[60px] w-[50px] items-center justify-center rounded-xl border text-2xl font-bold dark:border-gray-700" :class="pin.length >= 2 ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 bg-white dark:bg-gray-900'" x-text="pin.length >= 2 ? '•' : ''"></div>
                    <div class="flex h-[60px] w-[50px] items-center justify-center rounded-xl border text-2xl font-bold dark:border-gray-700" :class="pin.length >= 3 ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 bg-white dark:bg-gray-900'" x-text="pin.length >= 3 ? '•' : ''"></div>
                    <div class="flex h-[60px] w-[50px] items-center justify-center rounded-xl border text-2xl font-bold dark:border-gray-700" :class="pin.length >= 4 ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 bg-white dark:bg-gray-900'" x-text="pin.length >= 4 ? '•' : ''"></div>
                </div>

                <p class="mt-3 min-h-[1.25rem] text-sm" :class="error ? 'text-red-600' : 'text-primary-700'" x-text="message"></p>

                <div class="mt-4 grid grid-cols-3 gap-2 px-2">
                    <button type="button" class="rounded-xl border border-gray-200 py-3 text-xl font-semibold hover:bg-primary-50 dark:border-gray-700" @click="press('1')">1</button>
                    <button type="button" class="rounded-xl border border-gray-200 py-3 text-xl font-semibold hover:bg-primary-50 dark:border-gray-700" @click="press('2')">2</button>
                    <button type="button" class="rounded-xl border border-gray-200 py-3 text-xl font-semibold hover:bg-primary-50 dark:border-gray-700" @click="press('3')">3</button>
                    <button type="button" class="rounded-xl border border-gray-200 py-3 text-xl font-semibold hover:bg-primary-50 dark:border-gray-700" @click="press('4')">4</button>
                    <button type="button" class="rounded-xl border border-gray-200 py-3 text-xl font-semibold hover:bg-primary-50 dark:border-gray-700" @click="press('5')">5</button>
                    <button type="button" class="rounded-xl border border-gray-200 py-3 text-xl font-semibold hover:bg-primary-50 dark:border-gray-700" @click="press('6')">6</button>
                    <button type="button" class="rounded-xl border border-gray-200 py-3 text-xl font-semibold hover:bg-primary-50 dark:border-gray-700" @click="press('7')">7</button>
                    <button type="button" class="rounded-xl border border-gray-200 py-3 text-xl font-semibold hover:bg-primary-50 dark:border-gray-700" @click="press('8')">8</button>
                    <button type="button" class="rounded-xl border border-gray-200 py-3 text-xl font-semibold hover:bg-primary-50 dark:border-gray-700" @click="press('9')">9</button>
                    <div></div>
                    <button type="button" class="rounded-xl border border-gray-200 py-3 text-xl font-semibold hover:bg-primary-50 dark:border-gray-700" @click="press('0')">0</button>
                    <button type="button" class="rounded-xl border border-red-200 py-3 text-lg font-semibold text-red-600 hover:bg-red-50 dark:border-red-900" @click="backspace()" aria-label="Backspace">⌫</button>
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
                        class="admin-input text-center tracking-[0.35em]"
                        placeholder="Or type PIN"
                        x-model="pin"
                        @input="pin = String(pin).replace(/\D/g, '').slice(0, 4); if (pin.length === 4) verify()"
                    >
                </div>
            </div>

            {{-- Screen 2: Actions --}}
            <div x-show="screen === 'action'" x-cloak class="relative py-2">
                <button type="button" class="absolute right-0 top-0 rounded-lg p-2 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800" @click="resetToPin()" aria-label="Close">✕</button>

                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-3xl text-primary-600 dark:bg-primary-900/30">✓</div>
                <h4 class="font-display text-xl font-bold text-gray-900 dark:text-white" x-text="'Hi! ' + userName"></h4>
                <p class="mt-2 whitespace-pre-line text-sm text-gray-500" x-text="message"></p>

                <div class="mt-6 grid gap-2">
                    <button type="button" class="rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold hover:bg-primary-50 disabled:opacity-60 dark:border-gray-700" x-show="state === 'not_checked_in'" @click="act('clock-in')" :disabled="loading">Clock In</button>
                    <button type="button" class="rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold hover:bg-primary-50 disabled:opacity-60 dark:border-gray-700" x-show="state === 'checked_in' || state === 'auto_checked_in'" @click="act('clock-out')" :disabled="loading">Clock Out</button>
                    <button type="button" class="rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold hover:bg-primary-50 disabled:opacity-60 dark:border-gray-700" x-show="state === 'checked_in' || state === 'auto_checked_in'" @click="act('start-break')" :disabled="loading">Start Break</button>
                    <button type="button" class="rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold hover:bg-primary-50 disabled:opacity-60 dark:border-gray-700" x-show="state === 'on_break'" @click="act('end-break')" :disabled="loading">End Break</button>
                </div>

                <button type="button" class="mt-3 text-sm font-semibold text-gray-500" @click="resetToPin()">Cancel</button>
            </div>
        </x-admin.card>
    </div>
</x-layouts.business-admin>
