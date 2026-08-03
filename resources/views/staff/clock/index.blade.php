<x-layouts.staff title="Clock In" active="clock">
    <div
        class="mx-auto max-w-md pb-24 sm:pb-0"
        x-data="staffClock({
            actionUrl: @js(route('staff.clock.action')),
            statusUrl: @js(route('staff.clock.status')),
            csrf: @js(csrf_token()),
            initialState: @js($state['state']),
            userName: @js($state['user']->name),
            hours: @js($state['hours']),
            breakEndsAt: @js($state['break']?->break_ended_at?->toIso8601String()),
        })"
    >
        <x-admin.card class="text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-50 text-3xl text-primary-600 dark:bg-primary-900/30">⏱</div>
            <h3 class="font-display text-2xl font-bold text-gray-900 dark:text-white" x-text="'Hi, ' + userName"></h3>
            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-500" x-text="message"></p>
            <p class="mt-1 text-sm font-semibold text-primary-700" x-show="hours !== null" x-text="hours !== null ? (Number(hours).toFixed(2) + 'h today') : ''"></p>

            <div class="mt-6 hidden grid-cols-1 gap-2 sm:grid">
                <button type="button" class="admin-touch-target w-full rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-60" x-show="state === 'not_checked_in'" @click="act('clock-in')" :disabled="loading">Clock In</button>
                <button type="button" class="admin-touch-target w-full rounded-xl border border-gray-200 px-4 text-sm font-semibold hover:bg-primary-50 disabled:opacity-60 dark:border-gray-700" x-show="state === 'checked_in' || state === 'auto_checked_in'" @click="act('clock-out')" :disabled="loading">Clock Out</button>
                <button type="button" class="admin-touch-target w-full rounded-xl border border-gray-200 px-4 text-sm font-semibold hover:bg-primary-50 disabled:opacity-60 dark:border-gray-700" x-show="state === 'checked_in' || state === 'auto_checked_in'" @click="act('start-break')" :disabled="loading">Start Break</button>
                <button type="button" class="admin-touch-target w-full rounded-xl border border-gray-200 px-4 text-sm font-semibold hover:bg-primary-50 disabled:opacity-60 dark:border-gray-700" x-show="state === 'on_break'" @click="act('end-break')" :disabled="loading">End Break</button>
            </div>

            <p class="mt-4 min-h-[1.25rem] text-sm" :class="error ? 'text-red-600' : 'text-primary-700'" x-text="statusMessage"></p>
        </x-admin.card>

        <div class="staff-mobile-actions mt-4 grid grid-cols-1 gap-2 sm:hidden">
            <button type="button" class="admin-touch-target w-full rounded-xl bg-primary-600 px-4 text-base font-semibold text-white hover:bg-primary-700 disabled:opacity-60" x-show="state === 'not_checked_in'" @click="act('clock-in')" :disabled="loading">Clock In</button>
            <button type="button" class="admin-touch-target w-full rounded-xl border border-gray-200 px-4 text-base font-semibold hover:bg-primary-50 disabled:opacity-60 dark:border-gray-700" x-show="state === 'checked_in' || state === 'auto_checked_in'" @click="act('clock-out')" :disabled="loading">Clock Out</button>
            <button type="button" class="admin-touch-target w-full rounded-xl border border-gray-200 px-4 text-base font-semibold hover:bg-primary-50 disabled:opacity-60 dark:border-gray-700" x-show="state === 'checked_in' || state === 'auto_checked_in'" @click="act('start-break')" :disabled="loading">Start Break</button>
            <button type="button" class="admin-touch-target w-full rounded-xl border border-gray-200 px-4 text-base font-semibold hover:bg-primary-50 disabled:opacity-60 dark:border-gray-700" x-show="state === 'on_break'" @click="act('end-break')" :disabled="loading">End Break</button>
        </div>
    </div>
</x-layouts.staff>
