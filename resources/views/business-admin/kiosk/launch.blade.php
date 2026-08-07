<x-layouts.business-admin title="Attendance Kiosk" active="kiosk">
    <x-admin.toolbar
        description="Launch a dedicated touch-screen clock terminal for your restaurant entrance."
    >
        <x-slot:actions>
            <x-admin.button href="{{ route('business-admin.kiosk.settings') }}" variant="secondary">Kiosk Settings</x-admin.button>
        </x-slot:actions>
    </x-admin.toolbar>

    <x-admin.card class="mx-auto max-w-xl">
        <div class="text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-3xl dark:bg-emerald-900/30">⏱</div>
            <h2 class="font-display text-xl font-bold text-gray-900 dark:text-white">Start Attendance Kiosk</h2>
            <p class="mt-2 text-sm text-gray-500">Select a branch and launch full-screen kiosk mode for staff PIN clock-in.</p>
        </div>

        <form method="POST" action="{{ route('business-admin.kiosk.activate') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="branch_id" class="admin-label">Branch</label>
                <select id="branch_id" name="branch_id" class="admin-input mt-1 w-full" required>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <p class="rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300">
                Staff at this branch will use their 4-digit PIN. You will stay logged in — exit kiosk mode with your admin password.
            </p>

            <x-admin.button type="submit" variant="primary" class="w-full justify-center">Launch Kiosk Mode</x-admin.button>
        </form>
    </x-admin.card>
</x-layouts.business-admin>
