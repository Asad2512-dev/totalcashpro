<x-layouts.business-admin title="{{ $supplier->name }}" active="suppliers">
    <x-admin.toolbar :title="$supplier->name" :description="$supplier->trading_name ?? 'Supplier profile'">
        <a href="{{ route('business-admin.suppliers') }}" class="text-sm font-semibold text-primary-700">← Suppliers</a>
    </x-admin.toolbar>

    @if (session('status'))
        <x-admin.alert class="mb-4">{{ session('status') }}</x-admin.alert>
    @endif

    <div class="mb-4 flex flex-wrap gap-2 text-sm">
        @foreach (['overview','products','contacts','performance'] as $t)
            <a href="{{ route('business-admin.suppliers.show', [$supplier, 'tab' => $t]) }}" class="rounded-xl px-3 py-1.5 {{ ($tab ?? 'overview') === $t ? 'bg-primary-600 text-white' : 'border' }}">{{ ucfirst($t) }}</a>
        @endforeach
    </div>

    @if (($tab ?? 'overview') === 'overview')
        <div class="admin-stat-grid mb-6">
            <x-admin.card><p class="text-xs uppercase text-gray-500">Status</p><p class="mt-2 font-bold">{{ $supplier->status?->label() ?? $supplier->status }}</p></x-admin.card>
            <x-admin.card><p class="text-xs uppercase text-gray-500">Orders</p><p class="mt-2 font-bold">{{ $performance['orders'] }}</p></x-admin.card>
            <x-admin.card><p class="text-xs uppercase text-gray-500">Total spend</p><p class="mt-2 font-bold">£{{ number_format($performance['total_spend'], 2) }}</p></x-admin.card>
            <x-admin.card><p class="text-xs uppercase text-gray-500">On-time rate</p><p class="mt-2 font-bold">{{ $performance['on_time_rate'] }}%</p></x-admin.card>
        </div>
        <x-admin.card>
            <dl class="grid gap-3 sm:grid-cols-2 text-sm">
                <div><dt class="text-gray-500">Email</dt><dd>{{ $supplier->email ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Phone</dt><dd>{{ $supplier->phone ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Payment terms</dt><dd>{{ $supplier->payment_terms ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Tax/VAT</dt><dd>{{ $supplier->tax_number ?? '—' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-gray-500">Address</dt><dd>{{ $supplier->address }}{{ $supplier->postcode ? ', '.$supplier->postcode : '' }}</dd></div>
            </dl>
        </x-admin.card>
    @elseif (($tab ?? '') === 'products')
        <x-admin.card>
            <h3 class="font-bold">Supplier products</h3>
            <x-admin.table class="mt-4" :columns="['Product','SKU','Unit cost','MOQ','Primary']" :rows="$supplier->products->map(fn ($p) => [
                $p->inventoryItem?->name ?? '—',
                $p->supplier_sku ?? '—',
                '£'.number_format((float) $p->unit_cost, 2),
                number_format((float) $p->moq, 0),
                $p->is_primary ? 'Yes' : 'No',
            ])->all()" />
        </x-admin.card>
    @elseif (($tab ?? '') === 'contacts')
        <x-admin.card>
            <h3 class="font-bold mb-4">Contacts</h3>
            <ul class="space-y-2 text-sm">
                @foreach ($supplier->contacts as $contact)
                    <li class="rounded-xl border p-3 dark:border-gray-700">{{ $contact->name }} · {{ $contact->role }} · {{ $contact->email }}</li>
                @endforeach
            </ul>
            <form method="POST" action="{{ route('business-admin.suppliers.contacts.store', $supplier) }}" class="mt-4 grid gap-3 sm:grid-cols-2">
                @csrf
                <x-admin.input name="name" label="Name" required />
                <x-admin.input name="role" label="Role" />
                <x-admin.input name="email" label="Email" type="email" />
                <x-admin.input name="phone" label="Phone" />
                <div class="sm:col-span-2"><x-admin.button type="submit" size="sm">Add contact</x-admin.button></div>
            </form>
        </x-admin.card>
    @else
        <x-admin.card>
            <h3 class="font-bold">Performance</h3>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2 text-sm">
                @foreach ($performance as $key => $value)
                    <div><dt class="text-gray-500">{{ str_replace('_', ' ', ucfirst($key)) }}</dt><dd class="font-semibold">{{ is_numeric($value) && str_contains($key, 'spend') ? '£'.number_format((float) $value, 2) : $value }}</dd></div>
                @endforeach
            </dl>
        </x-admin.card>
    @endif
</x-layouts.business-admin>
