<x-layouts.business-admin title="Inventory" active="inventory">
    <x-admin.toolbar title="Inventory" description="Branch stock levels, categories and live counts.">
        <x-admin.button variant="secondary" size="sm" :href="route('business-admin.inventory-history')">History</x-admin.button>
    </x-admin.toolbar>

    @if ($lowStock->isNotEmpty())
        <div class="mb-4">
            <x-admin.alert tone="warning">{{ $lowStock->count() }} item(s) at or below stock limit.</x-admin.alert>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-2">
        <x-admin.card>
            <h3 class="font-display font-bold">Add category</h3>
            <form method="POST" action="{{ route('business-admin.inventory.categories.store') }}" class="mt-4 space-y-3">
                @csrf
                <x-admin.input name="name" label="Name" required />
                <label class="block space-y-1.5">
                    <span class="text-sm font-medium">Branch</span>
                    <select name="branch_id" class="admin-input" required>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </label>
                <x-admin.button type="submit" size="sm">Save category</x-admin.button>
            </form>
        </x-admin.card>

        <x-admin.card>
            <h3 class="font-display font-bold">Add product</h3>
            <form method="POST" action="{{ route('business-admin.inventory.items.store') }}" class="mt-4 space-y-3">
                @csrf
                <x-admin.input name="name" label="Name" required />
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="block space-y-1.5">
                        <span class="text-sm font-medium">Branch</span>
                        <select name="branch_id" class="admin-input" required>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block space-y-1.5">
                        <span class="text-sm font-medium">Category</span>
                        <select name="category_id" class="admin-input">
                            <option value="">None</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <label class="block space-y-1.5">
                        <span class="text-sm font-medium">Packaging</span>
                        <select name="packaging" class="admin-input">
                            <option value="pcs">pcs</option>
                            <option value="box">box</option>
                            <option value="box.pcs">box.pcs</option>
                        </select>
                    </label>
                    <x-admin.input name="pcs_per_box" type="number" label="Pcs / box" value="1" min="1" />
                    <x-admin.input name="stock_total_pcs" type="number" label="Stock pcs" value="0" min="0" />
                </div>
                <x-admin.input name="stock_limit" type="number" label="Low stock limit" value="0" min="0" />
                <x-admin.button type="submit" size="sm">Save product</x-admin.button>
            </form>
        </x-admin.card>
    </div>

    <div class="mt-6">
        <form method="GET" class="mb-4 max-w-sm">
            <x-admin.input name="q" label="Search products" :value="request('q')" />
        </form>

        @if ($items->isEmpty())
            <x-admin.empty-state title="No products" description="Add products for a branch to start counting stock." />
        @else
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left">Product</th>
                            <th class="px-4 py-3 text-center">Stock</th>
                            <th class="px-4 py-3">Update count</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ $item->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->category?->name ?? 'Uncategorised' }} · {{ $item->branch?->name }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ $item->stock_total_pcs }} pcs
                                    @if ($item->isLowStock())
                                        <x-admin.badge tone="warning">Low</x-admin.badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('business-admin.inventory.counts.store') }}" class="flex flex-wrap items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                                        <input type="number" name="new_pcs" class="admin-input w-24" value="{{ $item->stock_total_pcs }}" min="0" required>
                                        <x-admin.button type="submit" size="sm">Save</x-admin.button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $items->links() }}</div>
        @endif
    </div>
</x-layouts.business-admin>
