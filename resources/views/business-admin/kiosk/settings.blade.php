<x-layouts.business-admin title="Kiosk Settings" active="kiosk">
    <x-admin.toolbar
        description="Configure welcome message, display options and session timeout for the attendance kiosk."
    >
        <x-slot:actions>
            @if ($kioskActive)
                <x-admin.button href="{{ route('business-admin.kiosk.index') }}" variant="primary">Open Kiosk</x-admin.button>
            @else
                <x-admin.button href="{{ route('business-admin.kiosk.index') }}" variant="secondary">Launch Kiosk</x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.toolbar>

    @if (session('status'))
        <x-admin.alert type="success" class="mb-4">{{ session('status') }}</x-admin.alert>
    @endif

    <x-admin.card class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('business-admin.kiosk.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="welcome_message" class="admin-label">Welcome message</label>
                <input type="text" id="welcome_message" name="welcome_message" class="admin-input mt-1 w-full" value="{{ old('welcome_message', $settings['welcome_message']) }}" maxlength="255" required>
                @error('welcome_message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="session_timeout_minutes" class="admin-label">Session timeout (minutes)</label>
                    <input type="number" id="session_timeout_minutes" name="session_timeout_minutes" class="admin-input mt-1 w-full" min="30" max="1440" value="{{ old('session_timeout_minutes', $settings['session_timeout_minutes']) }}" required>
                    @error('session_timeout_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="success_display_seconds" class="admin-label">Success screen (seconds)</label>
                    <input type="number" id="success_display_seconds" name="success_display_seconds" class="admin-input mt-1 w-full" min="2" max="10" value="{{ old('success_display_seconds', $settings['success_display_seconds']) }}" required>
                    @error('success_display_seconds')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <label class="flex items-center gap-3">
                <input type="checkbox" name="show_photos" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" @checked(old('show_photos', $settings['show_photos']))>
                <span class="text-sm text-gray-700 dark:text-gray-200">Show staff photos when available</span>
            </label>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Branches</p>
                <ul class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                    @foreach ($branches as $branch)
                        <li>{{ $branch->name }}</li>
                    @endforeach
                </ul>
                <p class="mt-3 text-xs text-gray-500">Select the active branch when launching kiosk mode.</p>
            </div>

            <x-admin.button type="submit" variant="primary">Save Settings</x-admin.button>
        </form>
    </x-admin.card>
</x-layouts.business-admin>
