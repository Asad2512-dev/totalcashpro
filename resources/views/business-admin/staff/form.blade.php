<x-layouts.business-admin title="{{ $staffMember ? 'Edit Staff' : 'Add Staff' }}" active="staff">
    <x-admin.toolbar :title="$staffMember ? 'Edit staff' : 'Add staff'" description="Assign branch, PIN and hourly rate." />

    <form method="POST" action="{{ $staffMember ? route('business-admin.staff.update', $staffMember) : route('business-admin.staff.store') }}" class="mx-auto max-w-3xl space-y-4">
        @csrf
        @if ($staffMember) @method('PUT') @endif
        <x-admin.card class="space-y-0">
            <div class="admin-form-grid gap-4">
            <div class="sm:col-span-2">
                <x-admin.input label="Full name" name="name" :value="old('name', $staffMember?->name)" required />
            </div>
            <x-admin.input label="Email" type="email" name="email" :value="old('email', $staffMember?->email)" required />
            <x-admin.input label="Phone" name="phone" :value="old('phone', $staffMember?->phone)" />
            <x-admin.input label="New kiosk PIN (4 digits)" name="pin_code" maxlength="4" placeholder="{{ $staffMember?->hasPinConfigured() ? 'Leave blank to keep current PIN' : 'e.g. 1234' }}" :value="old('pin_code')" />
            @if ($staffMember?->hasPinConfigured())
                <div class="sm:col-span-2">
                    <x-admin.button
                        type="button"
                        variant="secondary"
                        size="sm"
                        @click="if (confirm('Generate a new kiosk PIN? The old PIN will stop working immediately.')) { document.getElementById('reset-pin-form').submit(); }"
                    >
                        Generate new kiosk PIN
                    </x-admin.button>
                </div>
            @endif
            <x-admin.input label="Hourly rate" type="number" step="0.01" name="hourly_rate" :value="old('hourly_rate', $staffMember?->hourly_rate)" />
            <div class="sm:col-span-2">
                <label class="block space-y-1.5">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Branch</span>
                    <select name="branch_id" class="admin-input min-h-[44px]">
                        <option value="">Unassigned</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) old('branch_id', $staffMember?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="sm:col-span-2">
                <x-admin.input label="Address" name="address" :value="old('address', $staffMember?->address)" />
            </div>
            <div class="sm:col-span-2">
                <label class="block space-y-1.5">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Notes</span>
                    <textarea name="notes" class="admin-input" rows="3">{{ old('notes', $staffMember?->notes) }}</textarea>
                </label>
            </div>
            <x-admin.input label="{{ $staffMember ? 'New password (optional)' : 'Password (optional)' }}" type="password" name="password" />
            @if ($staffMember)
                <label class="block space-y-1.5">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Status</span>
                    <select name="status" class="admin-input min-h-[44px]">
                        <option value="active" @selected(old('status', $staffMember->status) === 'active')>Active</option>
                        <option value="suspended" @selected(old('status', $staffMember->status) === 'suspended')>Suspended</option>
                    </select>
                </label>
            @endif
            </div>
        </x-admin.card>
        <x-admin.form-actions>
            <x-admin.button variant="secondary" :href="route('business-admin.staff')">Cancel</x-admin.button>
            <x-admin.button type="submit">Save</x-admin.button>
        </x-admin.form-actions>
    </form>

    @if ($staffMember?->hasPinConfigured())
        <form id="reset-pin-form" method="POST" action="{{ route('business-admin.staff.reset-pin', $staffMember) }}" class="hidden">
            @csrf
        </form>
    @endif
</x-layouts.business-admin>
