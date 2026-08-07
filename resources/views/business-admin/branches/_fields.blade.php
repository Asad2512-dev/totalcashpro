@php
    $branch = $branch ?? null;
@endphp

<x-admin.input name="name" label="Branch name" :value="old('name', $branch?->name)" required />
<div class="grid gap-4 sm:grid-cols-2">
    <x-admin.input name="city" label="City" :value="old('city', $branch?->city)" />
    <x-admin.input name="postcode" label="Postcode" :value="old('postcode', $branch?->postcode)" />
</div>
<x-admin.textarea name="address" label="Address" rows="2">{{ old('address', $branch?->address) }}</x-admin.textarea>
<div class="grid gap-4 sm:grid-cols-2">
    <x-admin.input name="phone" label="Phone" :value="old('phone', $branch?->phone)" />
    <x-admin.input type="email" name="email" label="Email" :value="old('email', $branch?->email)" />
</div>
<div>
    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Manager</label>
    <select name="manager_user_id" class="admin-input w-full">
        <option value="">— None —</option>
        @foreach ($managers as $manager)
            <option value="{{ $manager->id }}" @selected((int) old('manager_user_id', $branch?->manager_user_id) === (int) $manager->id)>{{ $manager->name }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Bank account</label>
    <select name="finance_bank_account_id" class="admin-input w-full">
        <option value="">— None —</option>
        @foreach ($bankAccounts as $account)
            <option value="{{ $account->id }}" @selected((int) old('finance_bank_account_id', $branch?->finance_bank_account_id) === (int) $account->id)>{{ $account->name }}</option>
        @endforeach
    </select>
</div>
<x-admin.input type="number" step="0.01" min="0" name="drawer_opening_balance" label="Cash drawer balance (£)" :value="old('drawer_opening_balance', $branch?->cashDrawer?->current_balance)" />
<x-admin.textarea name="receipt_footer" label="Receipt footer" rows="2">{{ old('receipt_footer', $branch?->receipt_footer) }}</x-admin.textarea>
