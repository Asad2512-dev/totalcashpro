<x-layouts.business-admin title="VAT Summary" active="finance">
    <x-finance.page-header title="VAT Summary" description="Output VAT vs reclaimable input VAT for the current quarter." />
    <x-finance.nav active="vat" />

    @php
        $vatKpis = [
            ['label' => 'Output VAT', 'value' => '£'.number_format((float) ($report['output_vat'] ?? 0), 2), 'change' => 'On income', 'tone' => 'success'],
            ['label' => 'Input VAT', 'value' => '£'.number_format((float) ($report['input_vat'] ?? 0), 2), 'change' => 'On purchases', 'tone' => 'warning'],
            ['label' => 'VAT due', 'value' => '£'.number_format((float) ($report['vat_due'] ?? 0), 2), 'change' => 'Estimate', 'tone' => 'info'],
        ];
    @endphp

    <x-admin.mobile-kpi-grid :items="$vatKpis" class="mb-4" />

    <div class="admin-stat-grid--compact mb-4 hidden lg:grid">
        @foreach ($vatKpis as $stat)
            <x-admin.stat compact :label="$stat['label']" :value="$stat['value']" :change="$stat['change']" :tone="$stat['tone']" />
        @endforeach
    </div>

    <p class="text-sm text-gray-500">VAT is stored separately on each record. This summary is for management purposes — submit returns via HMRC or your accountant.</p>
</x-layouts.business-admin>
