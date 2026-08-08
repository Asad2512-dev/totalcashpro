<x-layouts.business-admin title="Accounting" active="accounting">
    <x-admin.mobile-page-header
        class="lg:hidden"
        title="Accounting"
        description="Bills, spendings and financial overview."
    >
        <x-slot:actions>
            @if ($tab === 'bills')
                <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-bill')">Add</x-admin.button>
            @elseif ($tab === 'spendings')
                <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-spending')">Add</x-admin.button>
            @endif
        </x-slot:actions>
    </x-admin.mobile-page-header>

    <x-admin.toolbar title="Accounting" description="Bills, spendings and a live financial overview for your business." class="hidden lg:flex">
        @if ($tab === 'bills')
            <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-bill')">Add bill</x-admin.button>
        @elseif ($tab === 'spendings')
            <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-spending')">Add spending</x-admin.button>
        @endif
    </x-admin.toolbar>

    <x-admin.action-grid class="mb-4">
        @foreach (['overview' => 'Overview', 'bills' => 'Bills', 'spendings' => 'Spendings'] as $key => $label)
            <x-admin.action-tile
                :href="route('business-admin.accounting', ['tab' => $key])"
                :label="$label"
                :variant="$tab === $key ? 'primary' : 'default'"
            />
        @endforeach
    </x-admin.action-grid>

    @if ($tab === 'overview')
        @php
            $overviewKpis = [
                ['label' => 'Cash up revenue', 'value' => '£'.number_format($overview['revenue'], 2), 'change' => 'This month', 'tone' => 'success'],
                ['label' => 'Payroll paid', 'value' => '£'.number_format($overview['payroll_out'], 2), 'change' => 'This month', 'tone' => 'info'],
                ['label' => 'Supplier invoices', 'value' => '£'.number_format($overview['supplier_bills'], 2), 'change' => 'Paid', 'tone' => 'info'],
                ['label' => 'Spendings', 'value' => '£'.number_format($overview['spendings'], 2), 'change' => 'This month', 'tone' => 'warning'],
                ['label' => 'Bills due', 'value' => '£'.number_format($overview['bills_due'], 2), 'change' => 'Outstanding', 'tone' => 'danger'],
                ['label' => 'Net position', 'value' => '£'.number_format($overview['net_position'], 2), 'change' => 'Revenue − outflows', 'tone' => $overview['net_position'] >= 0 ? 'success' : 'danger'],
            ];
        @endphp

        <x-admin.mobile-kpi-grid :items="$overviewKpis" class="mb-4" />

        <div class="admin-stat-grid--compact mb-4 hidden lg:grid">
            @foreach ($overviewKpis as $stat)
                <x-admin.stat compact :label="$stat['label']" :value="$stat['value']" :change="$stat['change']" :tone="$stat['tone']" />
            @endforeach
        </div>

        <div class="admin-panel-grid">
            <x-admin.card>
                <h3 class="font-display font-bold text-gray-900 dark:text-white">Quick links</h3>
                <x-admin.action-grid class="mt-3">
                    <x-admin.action-tile :href="route('business-admin.accounting', ['tab' => 'bills'])" icon="card" label="Bills" />
                    <x-admin.action-tile :href="route('business-admin.accounting', ['tab' => 'spendings'])" icon="cash" label="Spendings" />
                    <x-admin.action-tile :href="route('business-admin.payroll')" icon="users" label="Payroll" />
                    <x-admin.action-tile :href="route('business-admin.suppliers', ['tab' => 'invoices'])" icon="tag" label="Invoices" />
                    <x-admin.action-tile :href="route('business-admin.cash-history')" icon="chart" label="Cash history" />
                </x-admin.action-grid>
            </x-admin.card>
            <x-admin.card>
                <h3 class="font-display font-bold text-gray-900 dark:text-white">What we track</h3>
                <ul class="admin-compact-grid mt-3 text-sm text-gray-600 dark:text-gray-300">
                    <li class="admin-compact-item">Recurring & one-off bills</li>
                    <li class="admin-compact-item">Day-to-day spendings</li>
                    <li class="admin-compact-item">Payroll & supplier outflows</li>
                    <li class="admin-compact-item">Cash up revenue</li>
                </ul>
            </x-admin.card>
        </div>
    @elseif ($tab === 'bills')
        @if ($bills->isEmpty())
            <x-admin.empty-state title="No bills yet" description="Track rent, utilities, insurance and other recurring costs.">
                <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-bill')">Add bill</x-admin.button>
            </x-admin.empty-state>
        @else
            <div class="admin-mobile-records mb-3 lg:hidden">
                @foreach ($bills as $bill)
                    @php
                        $status = $bill->status instanceof \BackedEnum ? $bill->status->value : (string) $bill->status;
                        $pending = strcasecmp($status, 'Paid') !== 0;
                    @endphp
                    <article class="admin-mobile-record">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="admin-mobile-record__title">{{ $bill->title }}</p>
                                <p class="mt-0.5 text-sm text-gray-500">{{ $bill->vendor ?: 'No vendor' }} · Due {{ $bill->due_date?->format('d M Y') }}</p>
                            </div>
                            <span class="shrink-0 text-sm font-semibold">£{{ number_format((float) $bill->amount, 2) }}</span>
                        </div>
                        @if ($pending)
                            <form method="POST" action="{{ route('business-admin.accounting.bills.paid', $bill) }}" class="mt-3">
                                @csrf
                                <x-admin.button size="sm" variant="secondary" type="submit" class="w-full">Mark paid</x-admin.button>
                            </form>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="admin-card hidden overflow-hidden lg:block">
                <div class="admin-table-wrap">
                    <table class="admin-table min-w-full text-left text-sm">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Bill</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Due</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bills as $bill)
                                @php
                                    $status = $bill->status instanceof \BackedEnum ? $bill->status->value : (string) $bill->status;
                                    $pending = strcasecmp($status, 'Paid') !== 0;
                                @endphp
                                <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                                    <td class="px-4 py-3.5">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $bill->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $bill->vendor ?: '—' }}</p>
                                    </td>
                                    <td class="px-4 py-3.5 capitalize text-gray-700 dark:text-gray-200">{{ $billCategories[$bill->category] ?? $bill->category }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $bill->due_date?->format('d M Y') }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">£{{ number_format((float) $bill->amount, 2) }}</td>
                                    <td class="px-4 py-3.5 capitalize text-gray-700 dark:text-gray-200">{{ $status }}</td>
                                    <td class="px-4 py-3.5">
                                        @if ($pending)
                                            <form method="POST" action="{{ route('business-admin.accounting.bills.paid', $bill) }}">
                                                @csrf
                                                <x-admin.table-action type="submit" variant="success">Mark paid</x-admin.table-action>
                                            </form>
                                        @else
                                            {{ $bill->paid_date?->format('d M Y') ?? '—' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <x-admin.pagination :paginator="$bills" />
        @endif
    @else
        @if ($spendings->isEmpty())
            <x-admin.empty-state title="No spendings yet" description="Record supplies, maintenance, marketing and other day-to-day costs.">
                <x-admin.button size="sm" x-data @click="$dispatch('open-modal', 'add-spending')">Add spending</x-admin.button>
            </x-admin.empty-state>
        @else
            <div class="admin-mobile-records mb-3 lg:hidden">
                @foreach ($spendings as $spending)
                    <article class="admin-mobile-record">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="admin-mobile-record__title">{{ $spending->title }}</p>
                                <p class="mt-0.5 text-sm text-gray-500">{{ $spendingCategories[$spending->category] ?? $spending->category }} · {{ $spending->spent_date?->format('d M Y') }}</p>
                            </div>
                            <span class="shrink-0 text-sm font-semibold">£{{ number_format((float) $spending->amount, 2) }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="admin-card hidden overflow-hidden lg:block">
                <div class="admin-table-wrap">
                    <table class="admin-table min-w-full text-left text-sm">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Payment</th>
                                <th class="px-4 py-3">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($spendings as $spending)
                                <tr class="transition hover:bg-primary-50/40 dark:hover:bg-gray-800/70">
                                    <td class="px-4 py-3.5">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $spending->title }}</p>
                                        @if ($spending->notes)
                                            <p class="text-xs text-gray-500">{{ $spending->notes }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $spendingCategories[$spending->category] ?? $spending->category }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $spending->spent_date?->format('d M Y') }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">{{ $paymentMethods[$spending->payment_method] ?? ($spending->payment_method ?: '—') }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-200">£{{ number_format((float) $spending->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <x-admin.pagination :paginator="$spendings" />
        @endif
    @endif

    <x-admin.modal name="add-bill" title="Add bill" max-width="max-w-md">
        <form method="POST" action="{{ route('business-admin.accounting.bills.store') }}" class="space-y-4">
            @csrf
            <x-admin.input name="title" label="Bill title" required class="w-full" placeholder="e.g. Monthly rent" />
            <x-admin.input name="vendor" label="Vendor" class="w-full" placeholder="e.g. Landlord Ltd" />
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                <select name="category" required class="admin-input min-h-[44px] w-full">
                    @foreach ($billCategories as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-form-grid">
                <x-admin.input type="number" name="amount" label="Amount (£)" step="0.01" min="0" required class="w-full" />
                <x-admin.input type="date" name="due_date" label="Due date" required class="w-full" value="{{ now()->addWeek()->toDateString() }}" />
            </div>
            <x-admin.textarea name="notes" label="Notes" rows="2" class="w-full" />
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                <x-admin.button type="submit">Save bill</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>

    <x-admin.modal name="add-spending" title="Add spending" max-width="max-w-md">
        <form method="POST" action="{{ route('business-admin.accounting.spendings.store') }}" class="space-y-4">
            @csrf
            <x-admin.input name="title" label="Description" required class="w-full" placeholder="e.g. Cleaning supplies" />
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                <select name="category" required class="admin-input min-h-[44px] w-full">
                    @foreach ($spendingCategories as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="admin-form-grid">
                <x-admin.input type="number" name="amount" label="Amount (£)" step="0.01" min="0" required class="w-full" />
                <x-admin.input type="date" name="spent_date" label="Date" required class="w-full" value="{{ now()->toDateString() }}" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Payment method</label>
                <select name="payment_method" class="admin-input min-h-[44px] w-full">
                    <option value="">Select…</option>
                    @foreach ($paymentMethods as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <x-admin.textarea name="notes" label="Notes" rows="2" class="w-full" />
            <x-admin.form-actions class="justify-end pt-2">
                <x-admin.button type="button" variant="secondary" @click="$dispatch('close-modal')">Cancel</x-admin.button>
                <x-admin.button type="submit">Save spending</x-admin.button>
            </x-admin.form-actions>
        </form>
    </x-admin.modal>
</x-layouts.business-admin>
