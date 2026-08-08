<x-layouts.business-admin title="Procurement" active="procurement">
    <x-admin.toolbar title="Procurement dashboard" description="Supplier orders, deliveries, receiving and invoices" />

    <div class="admin-stat-grid mb-6">
        <x-admin.card><p class="text-xs font-semibold uppercase text-gray-500">Active suppliers</p><p class="mt-2 text-2xl font-bold">{{ $summary['active_suppliers'] }}</p></x-admin.card>
        <x-admin.card><p class="text-xs font-semibold uppercase text-gray-500">Awaiting approval</p><p class="mt-2 text-2xl font-bold text-amber-600">{{ $summary['awaiting_approval'] }}</p></x-admin.card>
        <x-admin.card><p class="text-xs font-semibold uppercase text-gray-500">Awaiting receiving</p><p class="mt-2 text-2xl font-bold text-primary-700">{{ $summary['awaiting_receiving'] }}</p></x-admin.card>
        <x-admin.card><p class="text-xs font-semibold uppercase text-gray-500">Disputed invoices</p><p class="mt-2 text-2xl font-bold text-red-600">{{ $summary['disputed_invoices'] }}</p></x-admin.card>
        <x-admin.card><p class="text-xs font-semibold uppercase text-gray-500">Month spend</p><p class="mt-2 text-2xl font-bold">£{{ number_format((float) $summary['month_spend'], 2) }}</p></x-admin.card>
        <x-admin.card><p class="text-xs font-semibold uppercase text-gray-500">Outstanding bills</p><p class="mt-2 text-2xl font-bold">£{{ number_format((float) $summary['outstanding_bills'], 2) }}</p></x-admin.card>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <x-admin.card>
            <h3 class="font-display text-lg font-bold">Operations</h3>
            <ul class="mt-3 space-y-2 text-sm">
                <li class="flex justify-between"><span>In transit</span><span class="font-semibold">{{ $summary['in_transit'] }}</span></li>
                <li class="flex justify-between"><span>Awaiting pickup</span><span class="font-semibold">{{ $summary['awaiting_pickup'] }}</span></li>
                <li class="flex justify-between"><span>Partial receipts</span><span class="font-semibold">{{ $summary['partial_receipts'] }}</span></li>
            </ul>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('business-admin.receiving.index') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white">Receiving</a>
                <a href="{{ route('business-admin.purchase-orders.index') }}" class="rounded-xl border px-4 py-2 text-sm font-semibold">Purchase orders</a>
            </div>
        </x-admin.card>

        <x-admin.card>
            <h3 class="font-display text-lg font-bold">Top suppliers by spend</h3>
            <ul class="mt-3 space-y-2 text-sm">
                @forelse ($summary['top_suppliers'] as $row)
                    <li class="flex justify-between">
                        <span>{{ $row->supplier?->name ?? 'Supplier' }}</span>
                        <span class="font-semibold">£{{ number_format((float) $row->spend, 2) }}</span>
                    </li>
                @empty
                    <li class="text-gray-500">No supplier spend recorded yet.</li>
                @endforelse
            </ul>
        </x-admin.card>
    </div>
</x-layouts.business-admin>
