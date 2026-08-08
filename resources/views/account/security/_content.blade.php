<div class="mx-auto max-w-4xl">
    <x-admin.toolbar description="Two-factor authentication, devices, login history and password." />

    @if (session('success'))
        <x-admin.alert type="success" class="mt-4">{{ session('success') }}</x-admin.alert>
    @endif

    @if (session('recovery_codes'))
        <x-admin.card class="mt-6">
            <h3 class="font-semibold text-gray-900 dark:text-white">Recovery codes — save these now</h3>
            <ul class="mt-3 grid gap-2 font-mono text-sm sm:grid-cols-2">
                @foreach (session('recovery_codes') as $code)
                    <li class="rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800">{{ $code }}</li>
                @endforeach
            </ul>
        </x-admin.card>
    @endif

    <x-admin.card class="mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Two-factor authentication</h3>
        <p class="mt-1 text-sm text-gray-500">Email OTP — authenticator apps can be added in a future release.</p>

        @if ($twoFactorEnabled)
            <p class="mt-4 text-sm text-primary-700 dark:text-primary-300">2FA is enabled on your account.</p>
            <form method="POST" action="{{ route($securityRoutePrefix.'.two-factor.disable') }}" class="mt-4 space-y-3">
                @csrf
                <x-admin.input type="password" name="password" label="Confirm password to disable" required />
                <x-admin.button type="submit" variant="secondary" size="sm">Disable 2FA</x-admin.button>
            </form>
        @else
            <form method="POST" action="{{ route($securityRoutePrefix.'.two-factor.enable') }}" class="mt-4">
                @csrf
                <x-admin.button type="submit" size="sm">Send setup code</x-admin.button>
            </form>
            @if (session('status'))
                <form method="POST" action="{{ route($securityRoutePrefix.'.two-factor.confirm') }}" class="mt-4 flex flex-wrap items-end gap-3">
                    @csrf
                    <x-admin.input name="otp" label="Enter 6-digit code" maxlength="6" required class="max-w-xs" />
                    <x-admin.button type="submit" size="sm">Enable 2FA</x-admin.button>
                </form>
            @endif
        @endif
    </x-admin.card>

    <x-admin.card class="mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Change password</h3>
        <form method="POST" action="{{ route($securityRoutePrefix.'.password.update') }}" class="mt-4 space-y-4">
            @csrf
            <x-admin.input type="password" name="current_password" label="Current password" required />
            <x-admin.input type="password" name="password" label="New password" required />
            <x-admin.input type="password" name="password_confirmation" label="Confirm new password" required />
            <x-admin.button type="submit" size="sm">Update password</x-admin.button>
        </form>
    </x-admin.card>

    <x-admin.card class="mt-6">
        <div class="flex items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Active devices</h3>
            <form method="POST" action="{{ route($securityRoutePrefix.'.devices.logout-all') }}">
                @csrf
                <x-admin.button type="submit" variant="secondary" size="sm">Sign out all others</x-admin.button>
            </form>
        </div>
        <div class="mt-4 space-y-3">
            @forelse ($devices as $device)
                <div class="flex flex-col gap-3 rounded-xl border border-gray-200 p-4 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $device->device_name }}</p>
                        <p class="text-sm text-gray-500">{{ $device->ip_address }} · Last active {{ $device->last_active_at?->diffForHumans() ?? '—' }}</p>
                        @if ($device->is_current)
                            <span class="mt-1 inline-block text-xs font-semibold text-primary-600">Current session</span>
                        @endif
                        @if ($device->is_trusted)
                            <span class="mt-1 inline-block text-xs font-semibold text-emerald-600">Trusted</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if (! $device->is_trusted)
                            <form method="POST" action="{{ route($securityRoutePrefix.'.devices.trust', $device) }}">
                                @csrf
                                <x-admin.button type="submit" size="sm" variant="secondary">Trust</x-admin.button>
                            </form>
                        @endif
                        @if (! $device->is_current)
                            <form method="POST" action="{{ route($securityRoutePrefix.'.devices.logout', $device) }}">
                                @csrf
                                <x-admin.button type="submit" size="sm" variant="secondary">Sign out</x-admin.button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No active devices recorded yet.</p>
            @endforelse
        </div>
    </x-admin.card>

    <x-admin.card class="mt-6 overflow-hidden" :padding="false">
        <div class="border-b border-gray-200 px-4 py-4 sm:px-5 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent login history</h3>
        </div>
        <x-admin.table
            bare
            :columns="['Date', 'IP', 'Browser', 'Event', 'Status']"
            :rows="collect($loginHistories->items())->map(fn ($entry) => [
                $entry->logged_in_at->format('d M Y H:i'),
                $entry->ip_address ?? '—',
                $entry->browser ?? '—',
                ucfirst($entry->event_type ?? 'login'),
                $entry->success ? 'Success' : 'Failed',
            ])->all()"
            empty="No login history yet."
        />
    </x-admin.card>

    <x-admin.card class="mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Change email</h3>
        <p class="mt-1 text-sm text-gray-500">Current: {{ $user->email }}</p>
        <form method="POST" action="{{ route($securityRoutePrefix.'.email.request') }}" class="mt-4 flex flex-wrap items-end gap-3">
            @csrf
            <x-admin.input type="email" name="email" label="New email address" required class="max-w-sm" />
            <x-admin.button type="submit" size="sm">Send verification code</x-admin.button>
        </form>
        @if (session('status'))
            <form method="POST" action="{{ route($securityRoutePrefix.'.email.confirm') }}" class="mt-4 flex flex-wrap items-end gap-3">
                @csrf
                <x-admin.input name="otp" label="Confirm with OTP" maxlength="6" required class="max-w-xs" />
                <x-admin.button type="submit" size="sm">Confirm email change</x-admin.button>
            </form>
        @endif
    </x-admin.card>

    <x-admin.card class="mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notification preferences</h3>
        <form method="POST" action="{{ route($securityRoutePrefix.'.notifications.update') }}" class="mt-4 space-y-3">
            @csrf
            @foreach ($notificationPrefs as $key => $pref)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 p-3 dark:border-gray-700">
                    <span class="text-sm font-medium">{{ $pref['label'] }}</span>
                    <div class="flex gap-4 text-sm">
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="preferences[{{ $key }}][email]" value="0">
                            <input type="checkbox" name="preferences[{{ $key }}][email]" value="1" @checked($pref['email'])> Email
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="hidden" name="preferences[{{ $key }}][database]" value="0">
                            <input type="checkbox" name="preferences[{{ $key }}][database]" value="1" @checked($pref['database'])> In-app
                        </label>
                    </div>
                </div>
            @endforeach
            <x-admin.button type="submit" size="sm">Save preferences</x-admin.button>
        </form>
    </x-admin.card>

    <x-admin.card class="mt-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Security activity</h3>
        <ul class="mt-4 space-y-2 text-sm">
            @forelse ($securityLogs as $log)
                <li class="flex justify-between gap-4 border-b border-gray-100 py-2 dark:border-gray-800">
                    <span>{{ $log->description }}</span>
                    <span class="shrink-0 text-gray-500">{{ $log->created_at->diffForHumans() }}</span>
                </li>
            @empty
                <li class="text-gray-500">No security events yet.</li>
            @endforelse
        </ul>
    </x-admin.card>
</div>
