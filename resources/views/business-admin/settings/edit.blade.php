<x-layouts.business-admin title="Settings" active="settings">
    <div class="mx-auto max-w-3xl">
        <div class="mt-6">
            <x-admin.card>
                <form method="POST" action="{{ route('business-admin.settings.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Organization Name *</label>
                        <x-admin.input name="name" value="{{ old('name', $organization?->name) }}" required class="mt-1 w-full" />
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Currency *</label>
                            <x-admin.select name="currency" required class="mt-1 w-full">
                                <option value="GBP" @selected(old('currency', $organization?->currency) === 'GBP')>GBP (£)</option>
                                <option value="EUR" @selected(old('currency', $organization?->currency) === 'EUR')>EUR (€)</option>
                                <option value="USD" @selected(old('currency', $organization?->currency) === 'USD')>USD ($)</option>
                            </x-admin.select>
                            @error('currency')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Timezone *</label>
                            <x-admin.select name="timezone" required class="mt-1 w-full">
                                <option value="Europe/London" @selected(old('timezone', $organization?->timezone) === 'Europe/London')>London</option>
                                <option value="Europe/Paris" @selected(old('timezone', $organization?->timezone) === 'Europe/Paris')>Paris</option>
                                <option value="America/New_York" @selected(old('timezone', $organization?->timezone) === 'America/New_York')>New York</option>
                            </x-admin.select>
                            @error('timezone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">VAT Number</label>
                            <x-admin.input name="vat_number" value="{{ old('vat_number', $organization?->vat_number) }}" class="mt-1 w-full" />
                            @error('vat_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Number</label>
                            <x-admin.input name="company_number" value="{{ old('company_number', $organization?->company_number) }}" class="mt-1 w-full" />
                            @error('company_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                        <x-admin.textarea name="address" rows="3" class="mt-1 w-full">{{ old('address', $organization?->address) }}</x-admin.textarea>
                        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <x-admin.input name="phone" value="{{ old('phone', $organization?->phone) }}" class="mt-1 w-full" />
                            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <x-admin.input type="email" name="email" value="{{ old('email', $organization?->email) }}" class="mt-1 w-full" />
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Additional Settings</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Strict Rota Clock-In</label>
                                    <p class="text-xs text-gray-500">Only allow staff to clock in during scheduled shifts</p>
                                </div>
                                <x-admin.switch name="settings[strict_rota_clockin]" :checked="old('settings.strict_rota_clockin', $organization?->settings['strict_rota_clockin'] ?? false) == '1'" />
                            </div>
                        </div>
                    </div>

                    <x-admin.form-actions class="justify-end border-t border-gray-200 pt-6 dark:border-gray-700">
                        <x-admin.button type="submit">Update Settings</x-admin.button>
                    </x-admin.form-actions>
                </form>
            </x-admin.card>
        </div>
    </div>
</x-layouts.business-admin>
