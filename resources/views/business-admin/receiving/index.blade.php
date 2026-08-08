<x-layouts.business-admin title="Goods receiving" active="receiving">
    <x-admin.toolbar title="Goods receiving" description="Verify delivered stock before inventory updates" />

    <x-admin.card>
        <div class="space-y-3 md:hidden">
            @foreach ($orders as $order)
                <a href="{{ route('business-admin.receiving.create', $order) }}" class="block rounded-xl border p-4 dark:border-gray-700">
                    <p class="font-semibold">{{ $order->po_number }}</p>
                    <p class="text-sm text-gray-500">{{ $order->supplier?->name }}</p>
                    <p class="mt-2 text-xs uppercase text-primary-700">{{ $order->status->label() }}</p>
                </a>
            @endforeach
        </div>
        <div class="hidden md:block">
            <x-admin.table :columns="['PO','Supplier','Status','']" :rows="$orders->map(fn ($o) => [
                $o->po_number,
                $o->supplier?->name,
                $o->status->label(),
                '<a href=\''.route('business-admin.receiving.create', $o).'\' class=\'text-primary-700 font-semibold\'>Receive</a>',
            ])->all()" />
        </div>
    </x-admin.card>
</x-layouts.business-admin>
