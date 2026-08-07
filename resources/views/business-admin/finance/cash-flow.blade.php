<x-layouts.business-admin title="Cash Flow" active="finance">
    <x-admin.toolbar title="Cash Flow" description="Money in vs money out for the current month." />
    <x-finance.nav active="cash-flow" />

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-admin.stat-card label="Inflows" :value="'£'.number_format((float) ($report['inflows'] ?? 0), 2)" tone="success" />
        <x-admin.stat-card label="Outflows" :value="'£'.number_format((float) ($report['outflows'] ?? 0), 2)" tone="warning" />
        <x-admin.stat-card label="Net cash flow" :value="'£'.number_format((float) ($report['net'] ?? 0), 2)" tone="info" />
    </div>

    <section class="admin-card p-5">
        <h2 class="font-semibold text-gray-900 dark:text-white">Outflow breakdown</h2>
        <dl class="mt-4 space-y-2 text-sm">
            @foreach (($report['breakdown'] ?? []) as $label => $amount)
                <div class="flex justify-between"><dt class="capitalize">{{ str_replace('_', ' ', $label) }}</dt><dd>£{{ number_format((float) $amount, 2) }}</dd></div>
            @endforeach
        </dl>
    </section>
</x-layouts.business-admin>
