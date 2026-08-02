<x-layouts.business-admin title="Clock In" active="clock-in">
    <div class="mx-auto max-w-md" x-data="clockInKeypad">
        <x-admin.card>
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Clock In / Out</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Enter your 4-digit PIN</p>
            </div>

            <div class="mt-6">
                <div class="mb-6 flex items-center justify-center gap-2">
                    <template x-for="i in 4" :key="i">
                        <div
                            :class="pin.length >= i ? 'bg-primary-600' : 'bg-gray-300'"
                            class="h-4 w-4 rounded-full transition"
                        ></div>
                    </template>
                </div>

                <div x-show="!verified" class="grid grid-cols-3 gap-3">
                    <template x-for="digit in [1,2,3,4,5,6,7,8,9,'',0,'⌫']" :key="digit">
                        <button
                            @click="handleKeypad(digit)"
                            :disabled="digit === ''"
                            class="flex h-16 items-center justify-center rounded-xl text-xl font-semibold transition hover:bg-gray-100 disabled:cursor-default disabled:hover:bg-transparent dark:hover:bg-gray-800"
                            :class="digit === '' ? '' : 'bg-white shadow-sm dark:bg-gray-900'"
                            x-text="digit"
                        ></button>
                    </template>
                </div>

                <div x-show="verified" x-transition class="space-y-4">
                    <div class="rounded-xl bg-primary-50 p-4 text-center dark:bg-primary-900/20">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Welcome back,</p>
                        <p class="text-xl font-bold text-primary-600" x-text="staffName"></p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" x-text="stateLabel"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button
                            @click="performAction('clock-in')"
                            x-show="state === 'not_checked_in'"
                            class="rounded-xl bg-primary-600 py-3 font-semibold text-white transition hover:bg-primary-700"
                        >
                            Clock In
                        </button>
                        <button
                            @click="performAction('clock-out')"
                            x-show="state === 'checked_in' || state === 'on_break'"
                            class="rounded-xl bg-red-600 py-3 font-semibold text-white transition hover:bg-red-700"
                        >
                            Clock Out
                        </button>
                        <button
                            @click="performAction('start-break')"
                            x-show="state === 'checked_in'"
                            class="rounded-xl bg-orange-600 py-3 font-semibold text-white transition hover:bg-orange-700"
                        >
                            Start Break
                        </button>
                        <button
                            @click="performAction('end-break')"
                            x-show="state === 'on_break'"
                            class="rounded-xl bg-blue-600 py-3 font-semibold text-white transition hover:bg-blue-700"
                        >
                            End Break
                        </button>
                    </div>

                    <button
                        @click="reset"
                        class="w-full rounded-xl border border-gray-200 py-3 font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Done
                    </button>
                </div>
            </div>
        </x-admin.card>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('clockInKeypad', () => ({
                pin: '',
                verified: false,
                staffName: '',
                state: '',
                stateLabel: '',

                handleKeypad(digit) {
                    if (digit === '⌫') {
                        this.pin = this.pin.slice(0, -1);
                    } else if (typeof digit === 'number' && this.pin.length < 4) {
                        this.pin += digit;
                        if (this.pin.length === 4) {
                            this.verifyPin();
                        }
                    }
                },

                async verifyPin() {
                    try {
                        const response = await fetch('{{ route('business-admin.clock-in.verify') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ pin: this.pin })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.staffName = data.staff.name;
                            this.state = data.state;
                            this.updateStateLabel(data.state, data.hours);
                            this.verified = true;
                        } else {
                            alert('Invalid PIN');
                            this.pin = '';
                        }
                    } catch (error) {
                        alert('Error verifying PIN');
                        this.pin = '';
                    }
                },

                async performAction(action) {
                    try {
                        const response = await fetch('{{ route('business-admin.clock-in.action') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ pin: this.pin, action })
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.state = data.state;
                            this.updateStateLabel(data.state, data.hours);
                        }
                    } catch (error) {
                        alert('Error performing action');
                    }
                },

                updateStateLabel(state, hours) {
                    if (state === 'not_checked_in') {
                        this.stateLabel = hours ? `Worked ${hours} hours today` : 'Not clocked in';
                    } else if (state === 'checked_in') {
                        this.stateLabel = 'Currently clocked in';
                    } else if (state === 'on_break') {
                        this.stateLabel = 'On break';
                    } else if (state === 'auto_checked_in') {
                        this.stateLabel = 'Automatically clocked in';
                    }
                },

                reset() {
                    this.pin = '';
                    this.verified = false;
                    this.staffName = '';
                    this.state = '';
                    this.stateLabel = '';
                }
            }));
        });
    </script>
    @endpush
</x-layouts.business-admin>
