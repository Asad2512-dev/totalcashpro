<x-layouts.kiosk
    title="Staff Clock"
    :organization-name="$organization?->name"
>
    <div
        class="kiosk-v2 flex h-[100dvh] flex-col overflow-hidden"
        x-data="kioskV2Terminal(@js([
            'sessionActive' => $sessionActive,
            'needsBranch' => $needsBranch,
            'sessionAdminEmail' => $sessionAdminEmail,
            'loginUrl' => route('kiosk.v2.login'),
            'selectBranchUrl' => route('kiosk.v2.select-branch'),
            'pinUrl' => route('kiosk.v2.pin'),
            'actionUrl' => route('kiosk.v2.action'),
            'logoutUrl' => route('kiosk.v2.logout'),
            'csrf' => csrf_token(),
            'branchName' => $branch?->name ?? '',
            'displayName' => $settings?->display_name ?? 'Staff Clock',
            'logoUrl' => $logoUrl,
            'branches' => $branches->map(fn ($b) => ['id' => $b->id, 'name' => $b->name])->values(),
            'selectedBranchId' => $session?->branch_id,
            'showAttendance' => (bool) ($settings?->show_attendance_list ?? true),
            'attendance' => $attendance,
        ]))"
        x-init="init()"
    >
        {{-- Login --}}
        <template x-if="!sessionActive && !needsBranch">
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-8">
                <img :src="logoUrl" alt="" class="mb-4 h-14 w-14 rounded-2xl border border-gray-200 bg-white object-contain p-1 dark:border-gray-700">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-primary-600">{{ brand_name() }}</p>
                <h1 class="mt-2 font-display text-2xl font-bold text-gray-900 dark:text-white">Staff Clock</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Business Admin Login</p>

                <form class="mt-8 w-full max-w-sm space-y-4" @submit.prevent="login">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" x-model="adminEmail" required class="admin-input w-full" autocomplete="username">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input type="password" x-model="adminPassword" required class="admin-input w-full" autocomplete="current-password">
                    </div>
                    <p x-show="loginError" x-text="loginError" class="text-sm text-red-600" x-cloak></p>
                    <button type="submit" class="admin-btn admin-btn-primary w-full" :disabled="loading">Login</button>
                </form>
            </div>
        </template>

        {{-- Branch selection --}}
        <template x-if="needsBranch">
            <div class="flex flex-1 flex-col items-center justify-center px-6 py-8">
                <h2 class="font-display text-xl font-bold text-gray-900 dark:text-white">Select Branch</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Choose which branch this kiosk operates for.</p>
                <div class="mt-6 w-full max-w-sm space-y-4">
                    <select x-model="selectedBranchId" class="admin-input w-full">
                        <option value="">Choose branch…</option>
                        <template x-for="branch in branches" :key="branch.id">
                            <option :value="branch.id" x-text="branch.name"></option>
                        </template>
                    </select>
                    <p x-show="loginError" x-text="loginError" class="text-sm text-red-600" x-cloak></p>
                    <button type="button" class="admin-btn admin-btn-primary w-full" @click="selectBranch()" :disabled="loading || !selectedBranchId">Continue</button>
                    <button type="button" class="admin-btn w-full" @click="logoutKiosk()">Cancel</button>
                </div>
            </div>
        </template>

        {{-- Active kiosk --}}
        <template x-if="sessionActive && !needsBranch">
            <div class="flex flex-1 flex-col">
                <header class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-800">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600">{{ brand_name() }}</p>
                        <p class="font-display text-lg font-bold leading-tight" x-text="branchName"></p>
                    </div>
                    <div class="text-right">
                        <p class="font-display text-xl font-bold tabular-nums" x-text="clockTime"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="clockDate"></p>
                    </div>
                </header>

                <div class="flex flex-1 flex-col lg:flex-row lg:overflow-hidden">
                    <div class="flex flex-1 flex-col items-center justify-center px-4 py-4">
                        <template x-if="screen === 'home'">
                            <div class="w-full max-w-md text-center">
                                <p class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400" x-text="displayName"></p>
                                <p class="mt-6 text-lg font-medium text-gray-700 dark:text-gray-200">Enter your PIN</p>
                                <div class="mx-auto mt-4 flex justify-center gap-3">
                                    <template x-for="i in 4" :key="i">
                                        <span class="h-3 w-3 rounded-full border-2 border-primary-500" :class="pin.length >= i ? 'bg-primary-500' : 'bg-transparent'"></span>
                                    </template>
                                </div>
                                <p x-show="message" x-text="message" class="mt-3 text-sm" :class="error ? 'text-red-600' : 'text-gray-500'" x-cloak></p>
                                <div class="mt-6 grid grid-cols-3 gap-3">
                                    <template x-for="key in ['1','2','3','4','5','6','7','8','9']" :key="key">
                                        <button type="button" class="kiosk-keypad-btn" @click="press(key)" x-text="key"></button>
                                    </template>
                                    <button type="button" class="kiosk-keypad-btn kiosk-keypad-btn-muted" @click="backspace()">←</button>
                                    <button type="button" class="kiosk-keypad-btn" @click="press(0)">0</button>
                                    <button type="button" class="kiosk-keypad-btn text-primary-600" @click="submitPin()">✓</button>
                                </div>
                            </div>
                        </template>

                        <template x-if="screen === 'choose' || screen === 'on_break'">
                            <div class="w-full max-w-md space-y-3 text-center">
                                <p class="text-lg font-semibold" x-text="actionMessage"></p>
                                <p class="text-sm text-gray-500" x-text="currentUser?.name"></p>
                                <template x-for="item in actionChoices" :key="item.action + (item.break_type || '')">
                                    <button type="button" class="admin-btn admin-btn-primary w-full py-4 text-base" @click="performAction(item)" x-text="item.label"></button>
                                </template>
                            </div>
                        </template>

                        <template x-if="screen === 'breaks'">
                            <div class="w-full max-w-md space-y-3 text-center">
                                <p class="text-lg font-semibold">Select Break</p>
                                <template x-for="item in breakOptions" :key="item.value">
                                    <button type="button" class="admin-btn w-full py-4 text-base" @click="performAction({ action: 'start-break', break_type: item.value, value: item.value })" x-text="item.label"></button>
                                </template>
                                <button type="button" class="admin-btn w-full" @click="screen = 'choose'">Back</button>
                            </div>
                        </template>

                        <template x-if="screen === 'success'">
                            <div class="text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 text-2xl text-primary-700 dark:bg-primary-900/40">✓</div>
                                <p class="mt-4 text-xl font-bold" x-text="successUser?.name"></p>
                                <p class="mt-1 text-primary-600" x-text="successLabel"></p>
                                <p class="mt-2 font-display text-3xl font-bold tabular-nums" x-text="successTime"></p>
                                <p x-show="successDetail" x-text="'Worked: ' + successDetail" class="mt-2 text-sm text-gray-500"></p>
                            </div>
                        </template>
                    </div>

                    <aside x-show="showAttendance && attendance.length" class="border-t border-gray-200 px-4 py-4 dark:border-gray-800 lg:w-72 lg:border-l lg:border-t-0" x-cloak>
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Working Now</p>
                        <ul class="mt-3 space-y-2 text-sm">
                            <template x-for="row in attendance" :key="row.name">
                                <li class="flex items-center justify-between rounded-xl bg-gray-100 px-3 py-2 dark:bg-gray-900">
                                    <span x-text="row.name"></span>
                                    <span class="text-xs text-gray-500" x-text="row.status"></span>
                                </li>
                            </template>
                        </ul>
                    </aside>
                </div>

                <footer class="flex items-center justify-between border-t border-gray-200 px-4 py-2 text-xs text-gray-500 dark:border-gray-800">
                    <button type="button" @click="showAdmin = !showAdmin" class="font-semibold text-primary-600">Admin</button>
                    <span x-text="sessionAdminEmail"></span>
                </footer>

                <div x-show="showAdmin" x-cloak class="absolute inset-0 z-20 flex items-end bg-black/40 sm:items-center sm:justify-center" @click.self="showAdmin = false">
                    <div class="w-full max-w-sm rounded-t-3xl bg-white p-6 dark:bg-gray-900 sm:rounded-3xl">
                        <h3 class="font-display text-lg font-bold">Kiosk Admin</h3>
                        <div class="mt-4 space-y-2">
                            <button type="button" class="admin-btn w-full" @click="screen = 'branch'; needsBranch = true; showAdmin = false">Change Branch</button>
                            <button type="button" class="admin-btn admin-btn-danger w-full" @click="logoutKiosk()">Exit Kiosk</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-layouts.kiosk>
