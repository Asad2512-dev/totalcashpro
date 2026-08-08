<x-layouts.business-admin title="Kiosk" active="kiosk">
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold text-gray-900 dark:text-white">Kiosk</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">One kiosk per business. Open <a href="{{ $kioskUrl }}" class="font-semibold text-primary-600" target="_blank" rel="noopener">{{ $kioskUrl }}</a> on your tablet and log in.</p>
            </div>
            <a href="{{ $kioskUrl }}" target="_blank" rel="noopener" class="admin-btn admin-btn-primary">Open Kiosk</a>
        </div>

        @if (session('status'))
            <x-admin.alert type="success">{{ session('status') }}</x-admin.alert>
        @endif
        @if (session('error'))
            <x-admin.alert type="error">{{ session('error') }}</x-admin.alert>
        @endif

        <x-admin.card title="Kiosk Settings">
            <form method="POST" action="{{ route('business-admin.kiosk.settings.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="admin-label">Display Name</label>
                        <x-admin.input name="display_name" value="{{ old('display_name', $settings->display_name) }}" class="mt-1 w-full" />
                    </div>
                    <div>
                        <label class="admin-label">Default Branch</label>
                        <x-admin.select name="default_branch_id" class="mt-1 w-full">
                            <option value="">—</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((int) old('default_branch_id', $settings->default_branch_id) === $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </x-admin.select>
                    </div>
                </div>

                <div class="flex flex-wrap gap-6">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="hidden" name="show_attendance_list" value="0">
                        <input type="checkbox" name="show_attendance_list" value="1" @checked(old('show_attendance_list', $settings->show_attendance_list)) class="rounded border-gray-300">
                        Show attendance list on kiosk
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="hidden" name="show_staff_names" value="0">
                        <input type="checkbox" name="show_staff_names" value="1" @checked(old('show_staff_names', $settings->show_staff_names)) class="rounded border-gray-300">
                        Show staff names
                    </label>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="admin-label">Session lifetime (minutes)</label>
                        <x-admin.input type="number" name="session_lifetime_minutes" value="{{ old('session_lifetime_minutes', $settings->session_lifetime_minutes) }}" class="mt-1 w-full" />
                    </div>
                    <div>
                        <label class="admin-label">Success screen delay (seconds)</label>
                        <x-admin.input type="number" name="success_delay_seconds" value="{{ old('success_delay_seconds', $settings->success_delay_seconds) }}" class="mt-1 w-full" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="admin-btn admin-btn-primary">Save Settings</button>
                </div>
            </form>
        </x-admin.card>

        <x-admin.card title="Active Session">
            @if ($activeSession)
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-gray-500">Admin</dt><dd class="font-medium">{{ $activeSession->startedBy?->email }}</dd></div>
                    <div><dt class="text-gray-500">Branch</dt><dd class="font-medium">{{ $activeSession->branch?->name ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">Started</dt><dd class="font-medium">{{ $activeSession->started_at?->format('d M Y H:i') }}</dd></div>
                    <div><dt class="text-gray-500">Last activity</dt><dd class="font-medium">{{ $activeSession->last_activity_at?->format('d M Y H:i') ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">IP</dt><dd class="font-medium">{{ $activeSession->ip_address }}</dd></div>
                    <div><dt class="text-gray-500">Device</dt><dd class="font-medium">{{ $activeSession->device_summary }}</dd></div>
                </dl>
                <form method="POST" action="{{ route('business-admin.kiosk.revoke-session') }}" class="mt-4" onsubmit="return confirm('Revoke this kiosk session?')">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn-danger">Revoke Session</button>
                </form>
            @else
                <p class="text-sm text-gray-500">No active kiosk session.</p>
            @endif
        </x-admin.card>

        <x-admin.card title="Break Types">
            <div class="space-y-4">
                @foreach ($breakTypes as $type)
                    <form method="POST" action="{{ route('business-admin.kiosk.break-types.update', $type) }}" class="rounded-2xl border border-gray-200 p-4 dark:border-gray-800">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-3 sm:grid-cols-4">
                            <x-admin.input name="name" value="{{ $type->name }}" required />
                            <label class="inline-flex items-center gap-2 text-sm sm:col-span-1">
                                <input type="hidden" name="is_paid" value="0">
                                <input type="checkbox" name="is_paid" value="1" @checked($type->is_paid)> Paid
                            </label>
                            <x-admin.input type="number" name="max_duration_minutes" value="{{ $type->max_duration_minutes }}" placeholder="Max min" />
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" @checked($type->is_active)> Active
                            </label>
                        </div>
                        <div class="mt-3 flex justify-end">
                            <button type="submit" class="admin-btn text-sm">Update</button>
                        </div>
                    </form>
                @endforeach

                <form method="POST" action="{{ route('business-admin.kiosk.break-types.store') }}" class="rounded-2xl border border-dashed border-gray-300 p-4 dark:border-gray-700">
                    @csrf
                    <p class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Add break type</p>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <x-admin.input name="name" placeholder="Name" required />
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_paid" value="1"> Paid
                        </label>
                        <x-admin.input type="number" name="max_duration_minutes" placeholder="Max minutes" />
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="admin-btn admin-btn-primary text-sm">Add</button>
                    </div>
                </form>
            </div>
        </x-admin.card>
    </div>
</x-layouts.business-admin>
