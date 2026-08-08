<x-layouts.business-admin title="Executive" active="executive">
    <x-admin.toolbar title="Executive overview" :description="$data['range']['label'].' · vs previous period'">
        <a href="{{ route('business-admin.executive.print', request()->query()) }}" target="_blank" class="text-sm font-semibold text-primary-700">Print</a>
        <a href="{{ route('business-admin.executive.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="text-sm font-semibold text-primary-700">CSV</a>
    </x-admin.toolbar>

    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <select name="preset" class="rounded-xl border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900" onchange="this.form.submit()">
            @foreach ($presets as $p)
                <option value="{{ $p->value }}" @selected($preset === $p)>{{ $p->label() }}</option>
            @endforeach
        </select>
    </form>

    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 mb-6">
        @foreach (['revenue' => 'Revenue', 'gross_profit' => 'Gross profit', 'gross_margin' => 'Margin %', 'net_profit' => 'Net profit', 'cash_position' => 'Cash', 'accounts_payable' => 'Payables', 'inventory_value' => 'Inventory', 'payroll_due' => 'Payroll due', 'vat_position' => 'VAT'] as $key => $label)
            @php $kpi = $data['kpis'][$key] ?? null; @endphp
            @if ($kpi)
                <x-admin.card class="!p-3">
                    <p class="text-[10px] font-semibold uppercase text-gray-500">{{ $label }}</p>
                    <p class="mt-1 text-lg font-bold">{{ $key === 'gross_margin' ? $kpi['current'].'%' : '£'.number_format((float) $kpi['current'], 0) }}</p>
                    @if ($kpi['percent'] !== null)
                        <p class="text-xs {{ $kpi['trend'] === 'up' ? 'text-emerald-600' : ($kpi['trend'] === 'down' ? 'text-red-600' : 'text-gray-500') }}">
                            {{ $kpi['trend'] === 'up' ? '↑' : ($kpi['trend'] === 'down' ? '↓' : '→') }} {{ $kpi['percent'] }}%
                        </p>
                    @endif
                </x-admin.card>
            @endif
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-3 mb-6">
        <x-admin.card class="lg:col-span-2 !overflow-visible pb-1">
            <h3 class="font-display text-lg font-bold">Revenue trend</h3>
            <div class="admin-report-line-chart mt-4">
                <div class="flex h-32 items-end gap-1">
                    @php $trendPoints = array_slice($data['revenue_trend'] ?? [], -14); $trendMax = max(1, max(array_column($trendPoints, 'value') ?: [1])); @endphp
                    @foreach ($trendPoints as $point)
                        <div class="flex-1 rounded-t bg-primary-500/80" style="height: {{ max(4, ($point['value'] / $trendMax) * 100) }}%" title="{{ $point['label'] }}: £{{ number_format($point['value'], 0) }}"></div>
                    @endforeach
                </div>
                <div class="admin-report-line-chart__axis mt-3 flex gap-1 overflow-x-auto pb-1 text-[10px] font-medium text-gray-400 dark:text-gray-500">
                    @foreach ($trendPoints as $point)
                        <span class="min-w-[2rem] flex-1 whitespace-nowrap text-center">{{ $point['label'] }}</span>
                    @endforeach
                </div>
            </div>
        </x-admin.card>

        <x-admin.card>
            <h3 class="font-display text-lg font-bold">Needs attention</h3>
            <ul class="mt-3 space-y-2 text-sm">
                @forelse ($data['alerts'] as $alert)
                    <li class="rounded-lg border p-2 dark:border-gray-700">
                        <p class="font-semibold">{{ $alert->title }}</p>
                        <p class="text-xs text-gray-500">{{ $alert->message }}</p>
                        <div class="mt-2 flex gap-2">
                            <form method="POST" action="{{ route('business-admin.executive.alerts.resolve', $alert) }}">@csrf<button class="text-xs text-primary-700">Resolve</button></form>
                        </div>
                    </li>
                @empty
                    <li class="text-gray-500">No open alerts.</li>
                @endforelse
            </ul>
        </x-admin.card>
    </div>

    <x-admin.card class="mb-6">
        <h3 class="font-display text-lg font-bold">Actionable insights</h3>
        <ul class="mt-3 space-y-2 text-sm">
            @foreach ($data['insights'] as $insight)
                <li class="rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-900">{{ $insight['message'] }}</li>
            @endforeach
        </ul>
    </x-admin.card>

    <x-admin.card class="mb-6 hidden md:block">
        <h3 class="font-display text-lg font-bold">Branch performance</h3>
        <x-admin.table class="mt-4" :columns="['Branch','Revenue','Gross profit','Margin','Net profit','Health']" :rows="collect($data['branch_health'])->map(fn ($b) => [
            $b['branch'],
            '£'.number_format($b['revenue'], 0),
            '£'.number_format($b['gross_profit'], 0),
            $b['margin'].'%',
            '£'.number_format($b['net_profit'], 0),
            $b['health_score'].'/100',
        ])->all()" />
    </x-admin.card>

    <div class="space-y-3 md:hidden">
        @foreach ($data['branch_health'] as $branch)
            <div class="rounded-xl border p-3 dark:border-gray-700">
                <p class="font-semibold">{{ $branch['branch'] }} · {{ $branch['health_score'] }}/100</p>
                <p class="text-sm text-gray-500">Revenue £{{ number_format($branch['revenue'], 0) }} · Profit £{{ number_format($branch['net_profit'], 0) }}</p>
            </div>
        @endforeach
    </div>
</x-layouts.business-admin>
