<x-layouts.business-admin title="Customers" active="crm">
    <x-admin.toolbar title="Customers" description="CRM — manage customers, notes and visit history.">
        <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-customer')">Add customer</x-admin.button>
    </x-admin.toolbar>

    <form method="GET" action="{{ route('business-admin.crm') }}" class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-admin.input name="q" label="Search" :value="$search" placeholder="Name, email or phone" />
        <div>
            <label class="text-sm font-semibold text-gray-900 dark:text-white">Marketing</label>
            <select name="marketing" class="admin-input mt-2 w-full">
                <option value="">All customers</option>
                <option value="email" @selected($marketing === 'email')>Email opt-in</option>
                <option value="sms" @selected($marketing === 'sms')>SMS opt-in</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <x-admin.button type="submit" size="sm">Filter</x-admin.button>
            <x-admin.button :href="route('business-admin.crm')" size="sm" variant="secondary">Reset</x-admin.button>
        </div>
    </form>

    @if ($customers->isEmpty())
        <x-admin.empty-state title="No customers yet" description="Add your first customer to get started." />
    @else
        <x-admin.card :padding="false">
            <div class="admin-table-wrap">
                <table class="admin-table min-w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Marketing</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $customer->name }}</td>
                                <td class="px-4 py-3">{{ $customer->email ?: '—' }}</td>
                                <td class="px-4 py-3">{{ $customer->phone ?: '—' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    @if ($customer->marketing_preferences['email'] ?? false) Email @endif
                                    @if ($customer->marketing_preferences['sms'] ?? false) SMS @endif
                                    @if (! ($customer->marketing_preferences['email'] ?? false) && ! ($customer->marketing_preferences['sms'] ?? false)) — @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <x-admin.table-action :href="route('business-admin.crm.show', $customer)" variant="neutral">View</x-admin.table-action>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-admin.card>
    @endif

    <x-admin.modal name="add-customer" title="Add customer">
        <form method="POST" action="{{ route('business-admin.crm.store') }}" class="space-y-4">
            @csrf
            <x-admin.input name="name" label="Name" required />
            <x-admin.input type="email" name="email" label="Email" />
            <x-admin.input name="phone" label="Phone" />
            <x-admin.textarea name="notes" label="Notes" rows="3" />
            <div class="flex justify-end gap-2">
                <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                <x-admin.button type="submit">Save</x-admin.button>
            </div>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
