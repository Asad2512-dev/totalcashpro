<x-layouts.business-admin title="Tills" active="cash-drawers">
    <x-admin.toolbar title="Till management" description="Multiple tills per branch — opening floats, balances, transfers and cash up.">
        <a href="{{ route('business-admin.cash-up') }}" class="text-sm font-semibold text-primary-700 hover:underline">Cash Up →</a>
    </x-admin.toolbar>

    @if (session('status'))
        <x-admin.alert class="mb-4">{{ session('status') }}</x-admin.alert>
    @endif
    @if (session('error'))
        <x-admin.alert type="error" class="mb-4">{{ session('error') }}</x-admin.alert>
    @endif

    <div class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <x-admin.stat label="Total tills" :value="(string) $summary['total']" />
        <x-admin.stat label="Active" :value="(string) $summary['active']" tone="success" />
        <x-admin.stat label="Total cash" :value="'£'.number_format($summary['total_cash'], 2)" />
        <x-admin.stat label="Today's variance" :value="'£'.number_format($summary['today_variance'], 2)" :tone="abs($summary['today_variance']) < 0.01 ? 'success' : 'warning'" />
    </div>

    <form method="GET" class="admin-field-row mb-4">
        <label class="admin-field sm:max-w-xs">
            <span class="admin-label">Branch</span>
            <select name="branch_id" class="admin-input" onchange="this.form.submit()">
                <option value="">All branches</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
        </label>
    </form>

    <x-admin.card class="mb-6">
        <h3 class="mb-4 font-display text-lg font-bold text-gray-900 dark:text-white">Add till</h3>
        <form method="POST" action="{{ route('business-admin.cash-drawers.store') }}" class="admin-field-grid">
            @csrf
            <x-admin.input name="name" label="Till name" placeholder="Till 1" required />
            <x-admin.input name="code" label="Code" placeholder="TILL-01" />
            @if ($branches->count() > 1)
                <label class="admin-field">
                    <span class="admin-label">Branch</span>
                    <select name="branch_id" class="admin-input" required>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected($selectedBranchId === $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </label>
            @endif
            <x-admin.input type="number" step="0.01" name="default_opening_float" label="Opening float (£)" :value="$defaultFloat" />
            <x-admin.input name="notes" label="Notes" placeholder="Optional" />
            <div class="flex items-end">
                <button type="submit" class="admin-btn admin-btn-primary">Add till</button>
            </div>
        </form>
    </x-admin.card>

    @if ($drawers->isEmpty())
        <x-admin.empty-state title="No tills yet" description="Add a till for each physical cash drawer at this branch." />
    @else
        <div class="space-y-3 md:hidden">
            @foreach ($drawers as $drawer)
                <a href="{{ route('business-admin.cash-drawers.show', $drawer) }}" class="block rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $drawer->name }}</p>
                            <p class="text-xs text-gray-500">{{ $drawer->branch?->name }} · {{ $drawer->code }}</p>
                        </div>
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold dark:bg-gray-800">{{ $drawer->drawerStatus()->label() }}</span>
                    </div>
                    <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        <div><dt class="text-gray-500">Balance</dt><dd class="font-semibold">£{{ number_format((float) $drawer->current_balance, 2) }}</dd></div>
                        <div><dt class="text-gray-500">Float</dt><dd>£{{ number_format($drawer->defaultOpeningFloat(), 2) }}</dd></div>
                    </dl>
                </a>
            @endforeach
        </div>

        <div class="hidden md:block">
            <x-admin.table :columns="['Till', 'Code', 'Branch', 'Opening float', 'Balance', 'Status', 'Last cash up', '']">
                @foreach ($drawers as $drawer)
                    <tr>
                        <td class="font-semibold text-gray-900 dark:text-white">{{ $drawer->name }}</td>
                        <td>{{ $drawer->code }}</td>
                        <td>{{ $drawer->branch?->name }}</td>
                        <td>£{{ number_format($drawer->defaultOpeningFloat(), 2) }}</td>
                        <td>£{{ number_format((float) $drawer->current_balance, 2) }}</td>
                        <td>{{ $drawer->drawerStatus()->label() }}</td>
                        <td class="text-sm text-gray-500">
                            @if ($drawer->lastCashUp)
                                {{ $drawer->lastCashUp->cashup_date?->format('d M') }} {{ $drawer->lastCashUp->shift instanceof \BackedEnum ? $drawer->lastCashUp->shift->value : $drawer->lastCashUp->shift }}
                                <span class="block text-xs">{{ $drawer->lastCashUp->creator?->name }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right">
                            <x-admin.table-action :href="route('business-admin.cash-drawers.show', $drawer)">View</x-admin.table-action>
                            <x-admin.table-action :href="route('business-admin.cash-up', ['cash_drawer_id' => $drawer->id])" variant="primary">Cash up</x-admin.table-action>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        </div>
    @endif
</x-layouts.business-admin>
