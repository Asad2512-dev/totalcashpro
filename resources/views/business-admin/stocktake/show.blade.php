<x-layouts.business-admin title="Stocktake Review" active="stocktake">
    <x-admin.toolbar
        :title="$stocktake->branch?->name.' · Week '.$stocktake->week_start?->format('d M').' – '.$stocktake->week_end?->format('d M Y')"
        :description="'Staff: '.$stocktake->creator?->name.' · Status: '.($stocktake->status?->label() ?? $stocktake->status)"
    >
        <a href="{{ route('business-admin.stocktake.print', $stocktake) }}" target="_blank" class="text-sm font-semibold text-primary-700 hover:underline">Print</a>
        <a href="{{ route('business-admin.stocktake.index') }}" class="text-sm font-semibold text-primary-700 hover:underline">← All stocktakes</a>
    </x-admin.toolbar>

    @if (session('status'))
        <x-admin.alert class="mb-4">{{ session('status') }}</x-admin.alert>
    @endif

    <div class="mb-4 flex flex-wrap gap-2">
        @if (in_array($stocktake->status?->value ?? $stocktake->status, ['submitted']))
            <form method="POST" action="{{ route('business-admin.stocktake.review', $stocktake) }}">@csrf<button class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white">Start review</button></form>
        @endif
        @if (in_array($stocktake->status?->value ?? $stocktake->status, ['rejected', 'approved']))
            <form method="POST" action="{{ route('business-admin.stocktake.reopen', $stocktake) }}">@csrf<button class="rounded-xl border px-4 py-2 text-sm font-semibold">Reopen</button></form>
        @endif
        @if (in_array($stocktake->status?->value ?? $stocktake->status, ['submitted', 'under_review']))
            <form method="POST" action="{{ route('business-admin.stocktake.reject', $stocktake) }}" class="inline-flex items-center gap-2">
                @csrf
                <input type="text" name="reason" placeholder="Rejection reason" class="rounded-xl border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900">
                <button class="rounded-xl border px-4 py-2 text-sm font-semibold">Reject</button>
            </form>
        @endif
    </div>

    @if (in_array($stocktake->status?->value ?? $stocktake->status, ['submitted', 'under_review']))
        <form method="POST" action="{{ route('business-admin.stocktake.approve', $stocktake) }}">
            @csrf
            <x-admin.card class="mb-4">
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-xs uppercase text-gray-500 dark:border-gray-700">
                                <th class="py-2 pr-3">Item</th>
                                <th class="py-2 pr-3">Expected</th>
                                <th class="py-2 pr-3">Counted</th>
                                <th class="py-2 pr-3">Diff</th>
                                <th class="py-2 pr-3">Par</th>
                                <th class="py-2 pr-3">On order</th>
                                <th class="py-2 pr-3">Suggested</th>
                                <th class="py-2 pr-3">Order qty</th>
                                <th class="py-2 pr-3">Exclude</th>
                                <th class="py-2">Override reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stocktake->items as $index => $line)
                                <input type="hidden" name="adjustments[{{ $index }}][inventory_stocktake_item_id]" value="{{ $line->id }}">
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="py-3 pr-3 font-medium">{{ $line->item?->name }}</td>
                                    <td class="py-3 pr-3">{{ number_format((float) $line->system_qty, 1) }}</td>
                                    <td class="py-3 pr-3">{{ $line->counted_qty !== null ? number_format((float) $line->counted_qty, 1) : '—' }}</td>
                                    <td class="py-3 pr-3">{{ $line->difference_qty !== null ? number_format((float) $line->difference_qty, 1) : '—' }}</td>
                                    <td class="py-3 pr-3">{{ number_format((float) $line->par_level, 1) }}</td>
                                    <td class="py-3 pr-3">{{ number_format((float) $line->on_order_qty, 1) }}</td>
                                    <td class="py-3 pr-3 text-primary-700 font-semibold">{{ number_format((float) $line->suggested_order_qty, 1) }}</td>
                                    <td class="py-3 pr-3">
                                        <input type="number" step="0.001" min="0" name="adjustments[{{ $index }}][ordered_qty]" value="{{ $line->ordered_qty ?? $line->suggested_order_qty }}" class="w-24 rounded-lg border border-gray-200 px-2 py-1 dark:border-gray-700 dark:bg-gray-900">
                                    </td>
                                    <td class="py-3 pr-3">
                                        <input type="checkbox" name="adjustments[{{ $index }}][excluded_from_order]" value="1" @checked($line->excluded_from_order)>
                                    </td>
                                    <td class="py-3">
                                        <input type="text" name="adjustments[{{ $index }}][override_reason]" value="{{ $line->override_reason }}" placeholder="Required if qty changed" class="w-full min-w-[12rem] rounded-lg border border-gray-200 px-2 py-1 text-sm dark:border-gray-700 dark:bg-gray-900">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 md:hidden">
                    @foreach ($stocktake->items as $index => $line)
                        <input type="hidden" name="adjustments[{{ $index }}][inventory_stocktake_item_id]" value="{{ $line->id }}">
                        <div class="rounded-xl border border-gray-200 p-3 dark:border-gray-700">
                            <p class="font-semibold">{{ $line->item?->name }}</p>
                            <dl class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                <div><dt class="text-gray-500">Counted</dt><dd>{{ $line->counted_qty !== null ? number_format((float) $line->counted_qty, 1) : '—' }}</dd></div>
                                <div><dt class="text-gray-500">Suggested</dt><dd class="font-semibold text-primary-700">{{ number_format((float) $line->suggested_order_qty, 1) }}</dd></div>
                                <div><dt class="text-gray-500">Par</dt><dd>{{ number_format((float) $line->par_level, 1) }}</dd></div>
                                <div><dt class="text-gray-500">On order</dt><dd>{{ number_format((float) $line->on_order_qty, 1) }}</dd></div>
                            </dl>
                            <div class="mt-3 space-y-2">
                                <x-admin.input type="number" step="0.001" min="0" name="adjustments[{{ $index }}][ordered_qty]" label="Order qty" :value="$line->ordered_qty ?? $line->suggested_order_qty" />
                                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="adjustments[{{ $index }}][excluded_from_order]" value="1" @checked($line->excluded_from_order)> Exclude from order</label>
                                <x-admin.input type="text" name="adjustments[{{ $index }}][override_reason]" label="Override reason" :value="$line->override_reason" placeholder="Required if qty changed" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>

            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white">Approve & generate POs</button>
            </div>
        </form>
    @else
        <x-admin.card>
            <div class="hidden md:block">
                <x-admin.table
                    :columns="['Item', 'Expected', 'Counted', 'Diff', 'Par', 'On order', 'Suggested', 'Ordered', 'Vendor']"
                    :rows="$stocktake->items->map(fn ($line) => [
                        $line->item?->name ?? '—',
                        number_format((float) $line->system_qty, 1),
                        $line->counted_qty !== null ? number_format((float) $line->counted_qty, 1) : '—',
                        $line->difference_qty !== null ? number_format((float) $line->difference_qty, 1) : '—',
                        number_format((float) $line->par_level, 1),
                        number_format((float) $line->on_order_qty, 1),
                        number_format((float) $line->suggested_order_qty, 1),
                        $line->ordered_qty !== null ? number_format((float) $line->ordered_qty, 1) : '—',
                        $line->supplier?->name ?? $line->item?->supplier?->name ?? '—',
                    ])->all()"
                />
            </div>

            <div class="space-y-3 md:hidden">
                @foreach ($stocktake->items as $line)
                    <div class="rounded-xl border border-gray-200 p-3 dark:border-gray-700">
                        <p class="font-semibold">{{ $line->item?->name }}</p>
                        <dl class="mt-2 grid grid-cols-2 gap-2 text-sm">
                            <div><dt class="text-gray-500">Counted</dt><dd>{{ $line->counted_qty !== null ? number_format((float) $line->counted_qty, 1) : '—' }}</dd></div>
                            <div><dt class="text-gray-500">Ordered</dt><dd class="font-semibold text-primary-700">{{ $line->ordered_qty !== null ? number_format((float) $line->ordered_qty, 1) : '—' }}</dd></div>
                        </dl>
                    </div>
                @endforeach
            </div>
        </x-admin.card>
    @endif
</x-layouts.business-admin>
