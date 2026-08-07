<x-layouts.business-admin title="Finance" active="finance">
    <x-admin.toolbar title="Finance Dashboard" description="Real-time overview for the selected branch." />

    <x-finance.nav active="dashboard" />

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Income (period)" :value="'£'.number_format((float) ($snapshot['income'] ?? 0), 2)" change="Cash up + other income" tone="success" />
        <x-admin.stat-card label="Expenses (period)" :value="'£'.number_format((float) ($snapshot['expenses'] ?? 0), 2)" change="Approved & paid" tone="warning" />
        <x-admin.stat-card label="Bills due" :value="'£'.number_format((float) ($snapshot['bills_due'] ?? 0), 2)" :change="($snapshot['counts']['approved_bills'] ?? 0).' open'" tone="danger" />
        <x-admin.stat-card label="Payroll due" :value="'£'.number_format((float) ($snapshot['payroll_due'] ?? 0), 2)" :change="($snapshot['counts']['pending_payroll'] ?? 0).' staff'" tone="info" />
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="admin-card p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Net position</h2>
            <p class="mt-2 text-3xl font-bold text-primary-700">£{{ number_format((float) ($snapshot['net_position'] ?? 0), 2) }}</p>
            <dl class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                <div class="flex justify-between"><dt>Cash up revenue</dt><dd>£{{ number_format((float) ($snapshot['cash_up_income'] ?? 0), 2) }}</dd></div>
                <div class="flex justify-between"><dt>Other income</dt><dd>£{{ number_format((float) ($snapshot['manual_income'] ?? 0), 2) }}</dd></div>
                <div class="flex justify-between"><dt>Supplier payments</dt><dd>£{{ number_format((float) ($snapshot['supplier_payments'] ?? 0), 2) }}</dd></div>
            </dl>
        </section>

        <section class="admin-card p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Integration readiness</h2>
            <p class="mt-1 text-sm text-gray-500">Connect accounting and payments providers when you are ready.</p>
            <ul class="mt-4 space-y-2">
                @foreach ($integrations as $item)
                    <li class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700">
                        <span>{{ $item['provider']->label() }}</span>
                        <span class="font-medium capitalize text-gray-500">{{ $item['status'] }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</x-layouts.business-admin>
