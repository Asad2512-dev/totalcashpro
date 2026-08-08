<x-layouts.business-admin title="Cash History" active="cash-history">
    <x-admin.toolbar title="Cash history" description="Saved cash ups with reconciliation totals.">
        @foreach (['daily', 'weekly', 'monthly', 'yearly'] as $p)
            <x-admin.nav-pill
                :href="route('business-admin.cash-history', array_filter(['period' => $p, 'date' => $date, 'cash_drawer_id' => $selectedDrawerId, 'status' => $selectedStatus]))"
                :active="$period === $p"
            >{{ ucfirst($p) }}</x-admin.nav-pill>
        @endforeach
    </x-admin.toolbar>

    <form method="GET" class="admin-field-row mb-6">
        <input type="hidden" name="period" value="{{ $period }}">
        <x-admin.input type="date" name="date" label="Date" :value="$date" class="sm:max-w-[12rem]" />
        <label class="admin-field sm:max-w-[12rem]">
            <span class="admin-label">Till</span>
            <select name="cash_drawer_id" class="admin-input">
                <option value="">All tills</option>
                @foreach ($drawers as $drawer)
                    <option value="{{ $drawer->id }}" @selected($selectedDrawerId === $drawer->id)>{{ $drawer->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="admin-field sm:max-w-[12rem]">
            <span class="admin-label">Status</span>
            <select name="status" class="admin-input">
                <option value="">All statuses</option>
                @foreach (['draft', 'submitted', 'approved', 'locked', 'rejected'] as $statusOption)
                    <option value="{{ $statusOption }}" @selected($selectedStatus === $statusOption)>{{ ucfirst($statusOption) }}</option>
                @endforeach
            </select>
        </label>
        <button type="submit" class="inline-flex min-h-[44px] items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white">Apply</button>
    </form>

    <div class="mb-6">
        <x-admin.stat
            label="Period revenue (excl. opening float)"
            :value="'£'.number_format((float) $total, 2)"
            :change="$from->format('d M').' – '.$to->format('d M Y')"
            tone="success"
        />
    </div>

    @if ($rows->isEmpty())
        <x-admin.empty-state title="No cash ups in this period" description="Saved Morning/Evening cash ups will appear here in real time." />
    @else
        <div class="space-y-3 md:hidden">
            @foreach ($rows as $row)
                <a href="{{ route('business-admin.cash-history.show', $row) }}" class="block rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $row->branch?->name }} — {{ $row->cashDrawer?->name ?? 'Till' }}</p>
                            <p class="text-sm text-gray-500">{{ $row->cashup_date?->format('d M Y') }} · {{ $row->shift instanceof \BackedEnum ? $row->shift->value : $row->shift }}</p>
                        </div>
                        <span class="text-xs font-semibold uppercase text-gray-500">{{ $row->status?->value ?? $row->status ?? 'draft' }}</span>
                    </div>
                    <dl class="mt-3 grid grid-cols-3 gap-2 text-sm">
                        <div>
                            <dt class="text-gray-500">Expected</dt>
                            <dd class="font-semibold">£{{ number_format((float) ($row->expected_cash ?? $row->calculatedExpectedCash()), 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Actual</dt>
                            <dd class="font-semibold">£{{ number_format($row->physicalCashTotal(), 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Variance</dt>
                            @php $variance = $row->varianceAmount(); @endphp
                            <dd @class(['font-semibold', 'text-emerald-600' => abs($variance) < 0.005, 'text-amber-600' => abs($variance) >= 0.005])>
                                {{ abs($variance) < 0.005 ? 'Balanced' : '£'.number_format($variance, 2) }}
                            </dd>
                        </div>
                    </dl>
                </a>
            @endforeach
        </div>

        <div class="hidden md:block">
            <x-admin.table
                :columns="['Date', 'Shift', 'Branch', 'Till', 'Opening', 'Expected', 'Actual', 'Variance', 'Revenue', 'Status', '']"
                :rows="$rows->map(fn ($row) => [
                    $row->cashup_date?->format('d M Y'),
                    $row->shift instanceof \BackedEnum ? $row->shift->value : (string) $row->shift,
                    $row->branch?->name ?? '—',
                    $row->cashDrawer?->name ?? '—',
                    '£'.number_format((float) $row->effectiveOpeningFloat(), 2),
                    '£'.number_format((float) ($row->expected_cash ?? $row->calculatedExpectedCash()), 2),
                    '£'.number_format($row->physicalCashTotal(), 2),
                    abs($row->varianceAmount()) < 0.005 ? 'Balanced' : '£'.number_format($row->varianceAmount(), 2),
                    '£'.number_format($row->revenueTotal(), 2),
                    $row->status?->value ?? $row->status ?? 'draft',
                    'View',
                ])->all()"
            />
        </div>
    @endif
</x-layouts.business-admin>
