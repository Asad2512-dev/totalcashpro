<x-layouts.business-admin title="Profile" active="profile">
    <div class="mx-auto max-w-2xl">
        <x-admin.toolbar title="My Profile" />

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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
                            <x-admin.input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 w-full" />
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
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

                    <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Change Password</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                                <x-admin.input type="password" name="password" class="mt-1 w-full" />
                                <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                                <x-admin.input type="password" name="password_confirmation" class="mt-1 w-full" />
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:items-center sm:justify-end dark:border-gray-700">
                        <x-admin.button type="submit">Update Profile</x-admin.button>
                    </div>
                </form>
            </x-admin.card>
        </div>
    </div>
</x-layouts.business-admin>
