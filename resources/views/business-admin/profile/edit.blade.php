<x-layouts.business-admin title="Profile" active="profile">
    <div class="mx-auto max-w-2xl">
        <x-admin.toolbar title="My Profile" description="Update your personal details. Change email, password and security in Account Security." />

        <p class="mt-4 text-sm">
            <a href="{{ route('business-admin.security.index') }}" class="font-semibold text-primary-600 hover:text-primary-700">Account Security →</a>
        </p>

        <div class="mt-6">
            <x-admin.card>
                <form method="POST" action="{{ route('business-admin.profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                        <x-admin.input name="name" value="{{ old('name', $user->name) }}" required class="mt-1 w-full" />
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->email }}</p>
                            <p class="mt-1 text-xs text-gray-500">Change email in <a href="{{ route('business-admin.security.index') }}" class="text-primary-600 underline">Account Security</a>.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <x-admin.input name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 w-full" />
                            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                        <x-admin.textarea name="address" rows="3" class="mt-1 w-full">{{ old('address', $user->address) }}</x-admin.textarea>
                        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:items-center sm:justify-end dark:border-gray-700">
                        <x-admin.button type="submit">Update Profile</x-admin.button>
                    </div>
                </form>
            </x-admin.card>
        </div>
    </div>
</x-layouts.business-admin>
