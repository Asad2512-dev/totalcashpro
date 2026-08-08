<x-layouts.business-admin title="Petty cash" active="finance">
    <x-finance.page-header title="Petty cash" description="Track floats, top-ups and small expenses.">
        <x-slot:actions>
            <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-petty')">Add</x-admin.button>
        </x-slot:actions>
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-petty')">Add float</x-admin.button>
    </x-finance.page-header>
    <x-finance.nav active="petty-cash" />

    @if ($accounts->isEmpty())
        <x-admin.empty-state title="No petty cash floats" description="Create a float for each branch or custodian." />
    @else
        <div class="space-y-6">
            @foreach ($accounts as $account)
                <x-admin.card :title="$account->name">
                    <p class="text-sm text-gray-500">Balance: <strong class="text-gray-900 dark:text-white">£{{ number_format((float) $account->balance, 2) }}</strong></p>
                    <form method="POST" action="{{ route('business-admin.finance.petty-cash.transactions.store', $account) }}" class="mt-4 grid gap-4 sm:grid-cols-4">
                        @csrf
                        <div>
                            <label class="text-sm font-semibold">Type</label>
                            <select name="type" class="admin-input mt-2 w-full" required>
                                <option value="top_up">Top up</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>
                        <x-admin.input type="number" step="0.01" min="0.01" name="amount" label="Amount (£)" required />
                        <x-admin.input name="description" label="Description" />
                        <div class="flex items-end">
                            <x-admin.button type="submit" size="sm">Record</x-admin.button>
                        </div>
                    </form>
                </x-admin.card>
            @endforeach
        </div>
    @endif

    <x-admin.modal name="add-petty" title="Add petty cash float">
        <form method="POST" action="{{ route('business-admin.finance.petty-cash.store') }}" class="space-y-4">
            @csrf
            <x-admin.input name="name" label="Float name" required />
            <x-admin.input type="number" step="0.01" min="0" name="opening_balance" label="Opening balance (£)" />
            <div class="flex justify-end gap-2">
                <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                <x-admin.button type="submit">Create</x-admin.button>
            </div>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
