<x-layouts.staff title="Weekly Stocktake" active="stocktake">
    <div class="mx-auto max-w-lg space-y-4 pb-24">
        <div class="rounded-2xl border border-primary-200 bg-primary-50 p-4 dark:border-primary-900 dark:bg-primary-950/40">
            <p class="text-xs font-semibold uppercase tracking-wide text-primary-700 dark:text-primary-300">Weekly stocktake</p>
            <h1 class="font-display text-xl font-bold text-gray-900 dark:text-white">{{ $stocktake->branch?->name }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">
                {{ $stocktake->week_start?->format('d M') }} – {{ $stocktake->week_end?->format('d M Y') }}
                · {{ $stocktake->status?->label() ?? $stocktake->status }}
            </p>
        </div>

        @if (session('status'))
            <x-admin.alert>{{ session('status') }}</x-admin.alert>
        @endif

        @php
            $grouped = $stocktake->items->groupBy(fn ($line) => $line->item?->category?->name ?? 'Uncategorised');
        @endphp

        <form method="POST" action="{{ route('staff.stocktake.save') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="stocktake_id" value="{{ $stocktake->id }}">

            @foreach ($grouped as $category => $lines)
                <section class="rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                    <h2 class="border-b border-gray-100 px-4 py-3 text-sm font-bold uppercase tracking-wide text-gray-500 dark:border-gray-800">{{ $category }}</h2>
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($lines as $index => $line)
                            @php $item = $line->item; @endphp
                            <div class="p-4">
                                <input type="hidden" name="items[{{ $line->id }}][inventory_item_id]" value="{{ $item?->id }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $item?->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            Unit: {{ $item?->unit ?? $item?->packaging ?? 'pcs' }}
                                            · Par: {{ number_format((float) $line->par_level, 1) }} {{ $item?->unit ?? 'pcs' }}
                                        </p>
                                        <p class="text-xs text-gray-500">System: {{ number_format((float) $line->system_qty, 1) }}</p>
                                    </div>
                                    <div class="w-28 text-right">
                                        <label class="mb-1 block text-xs text-gray-500">Counted</label>
                                        <input
                                            type="number"
                                            step="0.001"
                                            min="0"
                                            name="items[{{ $line->id }}][counted_qty]"
                                            value="{{ $line->counted_qty }}"
                                            class="admin-input text-right"
                                            @disabled(! $stocktake->isEditableByStaff())
                                        >
                                    </div>
                                </div>
                                @if ($line->counted_qty !== null)
                                    <dl class="mt-2 grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <dt class="text-gray-500">Difference</dt>
                                            <dd class="font-semibold">{{ number_format((float) $line->difference_qty, 1) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-gray-500">Suggested order</dt>
                                            <dd class="font-semibold text-primary-700">{{ number_format((float) $line->suggested_order_qty, 1) }}</dd>
                                        </div>
                                    </dl>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach

            @if ($stocktake->isEditableByStaff())
                <div class="sticky bottom-20 flex gap-2">
                    <button type="submit" class="flex-1 rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white dark:bg-white dark:text-gray-900">Save counts</button>
                </div>
            @endif
        </form>

        @if ($stocktake->isEditableByStaff())
            <form method="POST" action="{{ route('staff.stocktake.submit') }}">
                @csrf
                <input type="hidden" name="stocktake_id" value="{{ $stocktake->id }}">
                <button type="submit" class="w-full rounded-xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white">Submit stocktake</button>
            </form>
        @endif
    </div>
</x-layouts.staff>
