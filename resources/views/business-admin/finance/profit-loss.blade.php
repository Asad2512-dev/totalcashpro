<x-layouts.business-admin title="Profit & Loss" active="finance">
    <x-finance.page-header title="Profit & Loss" description="Simple P&amp;L for hospitality and retail — not full statutory accounts." />
    <x-finance.nav active="profit-loss" />

    <div class="admin-card p-5">
        <dl class="admin-compact-grid text-sm">
            <div class="admin-compact-item">
                <dt class="admin-compact-item__meta">Revenue (net)</dt>
                <dd class="admin-compact-item__value">£{{ number_format((float) ($report['revenue_net'] ?? 0), 2) }}</dd>
            </div>
            <div class="admin-compact-item">
                <dt class="admin-compact-item__meta">Cost of sales</dt>
                <dd class="admin-compact-item__value">£{{ number_format((float) ($report['cost_of_sales'] ?? 0), 2) }}</dd>
            </div>
            <div class="admin-compact-item">
                <dt class="admin-compact-item__meta">Operating bills</dt>
                <dd class="admin-compact-item__value">£{{ number_format((float) ($report['operating_expenses'] ?? 0), 2) }}</dd>
            </div>
            <div class="admin-compact-item">
                <dt class="admin-compact-item__meta">Payroll</dt>
                <dd class="admin-compact-item__value">£{{ number_format((float) ($report['payroll'] ?? 0), 2) }}</dd>
            </div>
            <div class="admin-compact-item">
                <dt class="admin-compact-item__meta">Total costs</dt>
                <dd class="admin-compact-item__value">£{{ number_format((float) ($report['total_costs'] ?? 0), 2) }}</dd>
            </div>
            <div class="admin-compact-item border-primary-200 bg-primary-50 dark:border-primary-800 dark:bg-primary-900/30">
                <dt class="admin-compact-item__meta text-primary-700 dark:text-primary-300">Profit</dt>
                <dd class="admin-compact-item__value text-primary-700 dark:text-primary-300">£{{ number_format((float) ($report['profit'] ?? 0), 2) }}</dd>
            </div>
        </dl>
    </div>
</x-layouts.business-admin>
