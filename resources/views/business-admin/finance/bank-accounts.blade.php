<x-layouts.business-admin title="Bank Accounts" active="finance">
    <x-admin.toolbar title="Bank Accounts" description="Track business bank accounts for this organisation.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-account')">Add account</x-admin.button>
    </x-admin.toolbar>
    <x-finance.nav active="bank-accounts" />

    @if ($accounts->isEmpty())
        <x-admin.empty-state title="No bank accounts" description="Add your business current account to link payments." />
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($accounts as $account)
                <article class="admin-card p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $account->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $account->bank_name ?: 'Bank account' }}</p>
                        </div>
                        @if ($account->is_default)
                            <span class="rounded-full bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-800">Default</span>
                        @endif
                    </div>
                    <dl class="mt-4 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                        @if ($account->sort_code)<div>Sort code: {{ $account->sort_code }}</div>@endif
                        @if ($account->account_number_last4)<div>Account: •••• {{ $account->account_number_last4 }}</div>@endif
                        <div>Opening balance: £{{ number_format((float) $account->opening_balance, 2) }}</div>
                    </dl>
                </article>
            @endforeach
        </div>
    @endif

    <x-admin.modal name="add-account" title="Add bank account">
        <form method="POST" action="{{ route('business-admin.finance.bank-accounts.store') }}" class="space-y-4">
            @csrf
            <x-admin.form-field label="Account name" name="name" required />
            <x-admin.form-field label="Bank name" name="bank_name" />
            <x-admin.form-field label="Sort code" name="sort_code" />
            <x-admin.form-field label="Last 4 digits" name="account_number_last4" maxlength="4" />
            <x-admin.form-field label="Opening balance (£)" name="opening_balance" type="number" step="0.01" value="0" />
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_default" value="1" /> Default account</label>
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="submit">Save account</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
