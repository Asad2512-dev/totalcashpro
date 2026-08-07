<x-layouts.business-admin title="Profit & Loss" active="finance">
    <x-admin.toolbar title="Profit & Loss" description="Simple P&amp;L for hospitality and retail — not full statutory accounts." />
    <x-finance.nav active="profit-loss" />

    <div class="admin-card p-5">
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-800"><dt>Revenue (net)</dt><dd class="font-semibold">£{{ number_format((float) ($report['revenue_net'] ?? 0), 2) }}</dd></div>
            <div class="flex justify-between"><dt>Cost of sales / expenses</dt><dd>£{{ number_format((float) ($report['cost_of_sales'] ?? 0), 2) }}</dd></div>
            <div class="flex justify-between"><dt>Operating bills</dt><dd>£{{ number_format((float) ($report['operating_expenses'] ?? 0), 2) }}</dd></div>
            <div class="flex justify-between"><dt>Payroll</dt><dd>£{{ number_format((float) ($report['payroll'] ?? 0), 2) }}</dd></div>
            <div class="flex justify-between border-t border-gray-100 pt-3 dark:border-gray-800"><dt>Total costs</dt><dd>£{{ number_format((float) ($report['total_costs'] ?? 0), 2) }}</dd></div>
            <div class="flex justify-between text-lg font-bold text-primary-700"><dt>Profit</dt><dd>£{{ number_format((float) ($report['profit'] ?? 0), 2) }}</dd></div>
        </dl>
    </div>
</x-layouts.business-admin>
