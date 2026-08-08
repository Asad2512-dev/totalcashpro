<x-layouts.business-admin title="Finance" active="finance">
    <x-admin.mobile-page-header class="lg:hidden" title="Finance" description="Real-time overview for the selected branch." />
    <x-admin.toolbar class="hidden lg:flex" title="Finance Dashboard" description="Real-time overview for the selected branch." />

    <x-finance.nav active="dashboard" />

    @php
        $financeKpis = [
            ['label' => 'Income (period)', 'value' => '£'.number_format((float) ($snapshot['income'] ?? 0), 2), 'change' => 'Cash up + other income', 'tone' => 'success'],
            ['label' => 'Expenses (period)', 'value' => '£'.number_format((float) ($snapshot['expenses'] ?? 0), 2), 'change' => 'Approved & paid', 'tone' => 'warning'],
            ['label' => 'Bills due', 'value' => '£'.number_format((float) ($snapshot['bills_due'] ?? 0), 2), 'change' => ($snapshot['counts']['approved_bills'] ?? 0).' open', 'tone' => 'danger'],
        ];
        $financeSecondary = [
            ['label' => 'Payroll due', 'value' => '£'.number_format((float) ($snapshot['payroll_due'] ?? 0), 2), 'change' => ($snapshot['counts']['pending_payroll'] ?? 0).' staff', 'tone' => 'info'],
        ];
    @endphp

    @php
        $financeAll = array_merge($financeKpis, $financeSecondary);
    @endphp

    <x-admin.mobile-kpi-grid :items="$financeAll" class="mb-4" />

    <div class="admin-stat-grid mb-4 hidden lg:grid lg:mb-6">
        <x-admin.stat label="Income (period)" :value="'£'.number_format((float) ($snapshot['income'] ?? 0), 2)" change="Cash up + other income" tone="success" />
        <x-admin.stat label="Expenses (period)" :value="'£'.number_format((float) ($snapshot['expenses'] ?? 0), 2)" change="Approved & paid" tone="warning" />
        <x-admin.stat label="Bills due" :value="'£'.number_format((float) ($snapshot['bills_due'] ?? 0), 2)" :change="($snapshot['counts']['approved_bills'] ?? 0).' open'" tone="danger" />
        <x-admin.stat label="Payroll due" :value="'£'.number_format((float) ($snapshot['payroll_due'] ?? 0), 2)" :change="($snapshot['counts']['pending_payroll'] ?? 0).' staff'" tone="info" />
    </div>

    <div class="admin-section-grid lg:grid-cols-2 lg:mb-6">
        <section class="admin-card p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Net position</h2>
            <p class="mt-2 text-3xl font-bold text-primary-700">£{{ number_format((float) ($snapshot['net_position'] ?? 0), 2) }}</p>
            <dl class="mt-4 grid grid-cols-2 gap-2 text-sm text-gray-600 dark:text-gray-300">
                <div class="admin-compact-item">
                    <dt class="admin-compact-item__meta">Cash up revenue</dt>
                    <dd class="admin-compact-item__value">£{{ number_format((float) ($snapshot['cash_up_income'] ?? 0), 2) }}</dd>
                </div>
                <div class="admin-compact-item">
                    <dt class="admin-compact-item__meta">Other income</dt>
                    <dd class="admin-compact-item__value">£{{ number_format((float) ($snapshot['manual_income'] ?? 0), 2) }}</dd>
                </div>
                <div class="admin-compact-item col-span-2">
                    <dt class="admin-compact-item__meta">Supplier payments</dt>
                    <dd class="admin-compact-item__value">£{{ number_format((float) ($snapshot['supplier_payments'] ?? 0), 2) }}</dd>
                </div>
            </dl>
        </section>

        <section class="admin-card p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Integration readiness</h2>
            <p class="mt-1 text-sm text-gray-500">Connect accounting and payments providers when you are ready.</p>
            <ul class="admin-compact-grid mt-4">
                @foreach ($integrations as $item)
                    <li class="admin-compact-item">
                        <p class="admin-compact-item__title">{{ $item['provider']->label() }}</p>
                        <p class="admin-compact-item__meta capitalize">{{ $item['status'] }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</x-layouts.business-admin>
