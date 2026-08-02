<x-layouts.business-admin title="{{ $staffMember ? 'Edit Staff' : 'Add Staff' }}" active="staff">
    <x-admin.toolbar :title="$staffMember ? 'Edit staff' : 'Add staff'" description="Assign branch, PIN and hourly rate." />

    <form method="POST" action="{{ $staffMember ? route('business-admin.staff.update', $staffMember) : route('business-admin.staff.store') }}" class="mx-auto max-w-2xl space-y-4">
        @csrf
        @if ($staffMember) @method('PUT') @endif
        <x-admin.card class="space-y-4">
            <x-admin.input label="Full name" name="name" :value="old('name', $staffMember?->name)" required />
            <x-admin.input label="Email" type="email" name="email" :value="old('email', $staffMember?->email)" required />
            <x-admin.input label="Phone" name="phone" :value="old('phone', $staffMember?->phone)" />
            <div class="grid gap-4 sm:grid-cols-2">
                <x-admin.input label="PIN (4 digits)" name="pin_code" maxlength="4" :value="old('pin_code', $staffMember?->pin_code)" />
                <x-admin.input label="Hourly rate" type="number" step="0.01" name="hourly_rate" :value="old('hourly_rate', $staffMember?->hourly_rate)" />
            </div>
            <label class="block space-y-1.5">
                <span class="text-sm font-medium">Branch</span>
                <select name="branch_id" class="admin-input">
                    <option value="">Unassigned</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((string) old('branch_id', $staffMember?->branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </label>
            <x-admin.input label="Address" name="address" :value="old('address', $staffMember?->address)" />
            <label class="block space-y-1.5">
                <span class="text-sm font-medium">Notes</span>
                <textarea name="notes" class="admin-input" rows="3">{{ old('notes', $staffMember?->notes) }}</textarea>
            </label>
            <x-admin.input label="{{ $staffMember ? 'New password (optional)' : 'Password (optional)' }}" type="password" name="password" />
            @if ($staffMember)
                <label class="block space-y-1.5">
                    <span class="text-sm font-medium">Status</span>
                    <select name="status" class="admin-input">
                        <option value="active" @selected(old('status', $staffMember->status) === 'active')>Active</option>
                        <option value="suspended" @selected(old('status', $staffMember->status) === 'suspended')>Suspended</option>
                    </select>
                </label>
            @endif
        </x-admin.card>
        <div class="flex gap-3">
            <x-admin.button type="submit">Save</x-admin.button>
            <x-admin.button variant="secondary" :href="route('business-admin.staff')">Cancel</x-admin.button>
        </div>
    </form>
</x-layouts.business-admin>