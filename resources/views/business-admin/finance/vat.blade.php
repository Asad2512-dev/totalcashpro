<x-layouts.business-admin title="VAT Summary" active="finance">
    <x-admin.toolbar title="VAT Summary" description="Output VAT vs reclaimable input VAT for the current quarter." />
    <x-finance.nav active="vat" />

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-admin.stat-card label="Output VAT" :value="'£'.number_format((float) ($report['output_vat'] ?? 0), 2)" change="On income" tone="success" />
        <x-admin.stat-card label="Input VAT" :value="'£'.number_format((float) ($report['input_vat'] ?? 0), 2)" change="On purchases" tone="warning" />
        <x-admin.stat-card label="VAT due" :value="'£'.number_format((float) ($report['vat_due'] ?? 0), 2)" change="Estimate" tone="info" />
    </div>

    <p class="text-sm text-gray-500">VAT is stored separately on each record. This summary is for management purposes — submit returns via HMRC or your accountant.</p>
</x-layouts.business-admin>
