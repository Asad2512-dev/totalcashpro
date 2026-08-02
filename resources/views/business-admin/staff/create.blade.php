<x-layouts.business-admin title="Add Staff" active="staff">
    <div class="mx-auto max-w-2xl">
        <x-admin.toolbar title="Add Staff Member">
            <x-slot:actions>
                <a href="{{ route('business-admin.staff') }}" class="text-sm text-gray-600 hover:text-gray-700">← Back</a>
            </x-slot:actions>
        </x-admin.toolbar>

        <div class="mt-6">
            <x-admin.card>
                <form method="POST" action="{{ route('business-admin.staff.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                        <x-admin.input name="name" value="{{ old('name') }}" required class="mt-1 w-full" />
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
                            <x-admin.input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full" />
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <x-admin.input name="phone" value="{{ old('phone') }}" class="mt-1 w-full" />
                            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">4-Digit PIN</label>
                            <x-admin.input name="pin_code" maxlength="4" pattern="\d{4}" placeholder="1234" value="{{ old('pin_code') }}" class="mt-1 w-full" />
                            @error('pin_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hourly Rate (£)</label>
                            <x-admin.input type="number" name="hourly_rate" step="0.01" min="0" value="{{ old('hourly_rate') }}" class="mt-1 w-full" />
                            @error('hourly_rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch</label>
                        <x-admin.select name="branch_id" class="mt-1 w-full">
                            <option value="">All Branches</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </x-admin.select>
                        @error('branch_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                        <x-admin.textarea name="address" rows="2" class="mt-1 w-full">{{ old('address') }}</x-admin.textarea>
                        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <x-admin.textarea name="notes" rows="3" class="mt-1 w-full">{{ old('notes') }}</x-admin.textarea>
                        @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password (optional)</label>
                        <x-admin.input type="password" name="password" class="mt-1 w-full" />
                        <p class="mt-1 text-xs text-gray-500">Leave blank to auto-generate</p>
                        @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <a href="{{ route('business-admin.staff') }}">
                            <x-admin.button type="button" tone="secondary">Cancel</x-admin.button>
                        </a>
                        <x-admin.button type="submit">Create Staff</x-admin.button>
                    </div>
                </form>
            </x-admin.card>
        </div>
    </div>
</x-layouts.business-admin>
