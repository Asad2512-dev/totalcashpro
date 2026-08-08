<x-layouts.business-admin title="Cash Flow" active="finance">
    <x-finance.page-header title="Cash Flow" description="Money in vs money out for the current month." />
    <x-finance.nav active="cash-flow" />

    @php
        $cashFlowKpis = [
            ['label' => 'Inflows', 'value' => '£'.number_format((float) ($report['inflows'] ?? 0), 2), 'change' => 'This month', 'tone' => 'success'],
            ['label' => 'Outflows', 'value' => '£'.number_format((float) ($report['outflows'] ?? 0), 2), 'change' => 'This month', 'tone' => 'warning'],
            ['label' => 'Net cash flow', 'value' => '£'.number_format((float) ($report['net'] ?? 0), 2), 'change' => 'In − out', 'tone' => 'info'],
        ];
    @endphp

    <x-admin.mobile-kpi-grid :items="$cashFlowKpis" class="mb-4" />

    <div class="admin-stat-grid--compact mb-4 hidden lg:grid">
        @foreach ($cashFlowKpis as $stat)
            <x-admin.stat compact :label="$stat['label']" :value="$stat['value']" :change="$stat['change']" :tone="$stat['tone']" />
        @endforeach
    </div>

    <section class="admin-card p-5">
        <h2 class="font-semibold text-gray-900 dark:text-white">Outflow breakdown</h2>
        <dl class="admin-compact-grid mt-4 text-sm">
            @foreach (($report['breakdown'] ?? []) as $label => $amount)
                <div class="admin-compact-item">
                    <dt class="admin-compact-item__meta capitalize">{{ str_replace('_', ' ', $label) }}</dt>
                    <dd class="admin-compact-item__value">£{{ number_format((float) $amount, 2) }}</dd>
                </div>
            @endforeach
        </dl>
    </section>
</x-layouts.business-admin>
