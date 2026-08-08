@props([
    'report',
    'filter',
    'actionRoute',
    'exportRoute',
    'showToolbar' => true,
    'showCharts' => true,
    'toolbarTitle' => 'Reports',
    'toolbarDescription' => 'Financial performance and business overview.',
])

@php
    $kpis = $report['kpis'];
    $charts = $report['charts'];
    $query = $filter->toQueryArray();

    $kpiItems = [
        ['label' => 'Revenue', 'value' => '£'.number_format((float) $kpis['revenue'], 2), 'change' => 'Avg £'.number_format((float) $report['averages']['daily_revenue'], 2).'/day', 'tone' => 'success'],
        ['label' => 'Expenses', 'value' => '£'.number_format((float) $kpis['expenses'], 2), 'change' => 'Period total', 'tone' => 'warning'],
        ['label' => 'Profit', 'value' => '£'.number_format((float) $kpis['profit'], 2), 'change' => 'Estimated', 'tone' => $kpis['profit'] >= 0 ? 'success' : 'danger'],
    ];

    $kpiSecondary = [
        ['label' => 'Cash (net)', 'value' => '£'.number_format((float) $kpis['cash'], 2), 'change' => 'Cash ups', 'tone' => 'info'],
        ['label' => 'Cards', 'value' => '£'.number_format((float) $kpis['cards'], 2), 'change' => 'Card takings', 'tone' => 'neutral'],
        ['label' => 'Online orders', 'value' => '£'.number_format((float) $kpis['online_orders'], 2), 'change' => 'Delivery apps', 'tone' => 'info'],
        ['label' => 'Bills due', 'value' => '£'.number_format((float) $kpis['bills'], 2), 'change' => 'Outstanding', 'tone' => 'danger'],
        ['label' => 'Payroll', 'value' => '£'.number_format((float) $kpis['payroll'], 2), 'change' => '£'.number_format((float) $kpis['payroll_pending'], 2).' pending', 'tone' => 'warning'],
        ['label' => 'Supplier spend', 'value' => '£'.number_format((float) $kpis['supplier_payments'], 2), 'change' => 'Paid out', 'tone' => 'neutral'],
        ['label' => 'Staff hours', 'value' => number_format((float) $kpis['attendance_hours'], 1).'h', 'change' => 'Clocked time', 'tone' => 'info'],
        ['label' => 'Avg weekly revenue', 'value' => '£'.number_format((float) $report['averages']['weekly_revenue'], 2), 'change' => 'Period average', 'tone' => 'success'],
        ['label' => 'Low stock items', 'value' => (string) $kpis['low_stock'], 'change' => 'Needs attention', 'tone' => 'danger'],
    ];

    $activeFilterCount = (int) ($filter->datePreset->value !== 'this_month')
        + (int) ($filter->branchId !== null)
        + (int) (($filter->status ?? 'all') !== 'all')
        + (int) ($filter->employeeId !== null);
@endphp

<div class="report-center admin-page print:text-black" x-data="{ loadedAt: Date.now() }" x-init="document.addEventListener('visibilitychange', () => { if (document.visibilityState === 'visible' && Date.now() - loadedAt > 45000) { window.location.reload(); } })">
    <x-admin.mobile-page-header
        class="lg:hidden"
        :title="$toolbarTitle"
        :description="$toolbarDescription"
    />

    @if ($showToolbar)
        <x-admin.toolbar :description="$toolbarDescription" class="hidden lg:flex">
            <div class="admin-toolbar-actions flex flex-wrap items-center gap-2 print:hidden">
                @if (! empty($report['generated_at']))
                    <span class="inline-flex min-w-[5.5rem] text-xs text-gray-400" title="Reports refresh automatically when your data changes">
                        Live · {{ \Illuminate\Support\Carbon::parse($report['generated_at'])->format('H:i') }}
                    </span>
                @endif
                <a href="{{ route($exportRoute, array_merge($query, ['format' => 'csv'])) }}" class="inline-flex min-h-[44px] items-center rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200">CSV</a>
                <a href="{{ route($exportRoute, array_merge($query, ['format' => 'excel'])) }}" class="inline-flex min-h-[44px] items-center rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200">Excel</a>
                <button type="button" onclick="window.print()" class="inline-flex min-h-[44px] items-center rounded-xl bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700">Print</button>
            </div>
        </x-admin.toolbar>
    @endif

    <x-admin.filter-sheet title="Report filters" :active-count="$activeFilterCount">
        <form method="GET" action="{{ route($actionRoute) }}" id="report-filters-form" class="report-filters space-y-3">
            <label class="admin-field">
                <span class="admin-label">Date range</span>
                <select name="date_preset" class="admin-input w-full min-h-[44px]">
                    @foreach ($report['date_presets'] as $preset)
                        <option value="{{ $preset->value }}" @selected($filter->datePreset === $preset)>{{ $preset->label() }}</option>
                    @endforeach
                </select>
            </label>

            <label class="admin-field">
                <span class="admin-label">Branch</span>
                <select name="branch_id" class="admin-input w-full min-h-[44px]">
                    <option value="all" @selected($filter->branchId === null)>All branches</option>
                    @foreach ($report['branches'] as $branch)
                        <option value="{{ $branch->id }}" @selected($filter->branchId === $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="admin-field">
                <span class="admin-label">Report type</span>
                <select name="report_type" class="admin-input w-full min-h-[44px]">
                    @foreach ($report['report_types'] as $type)
                        <option value="{{ $type->value }}" @selected($filter->reportType === $type)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </label>

            <label class="admin-field">
                <span class="admin-label">Employee</span>
                <select name="employee_id" class="admin-input w-full min-h-[44px]">
                    <option value="all">All employees</option>
                    @foreach ($report['employees'] as $employee)
                        <option value="{{ $employee->id }}" @selected($filter->employeeId === $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="admin-field">
                <span class="admin-label">Status</span>
                <select name="status" class="admin-input w-full min-h-[44px]">
                    @foreach ($report['statuses'] as $status)
                        <option value="{{ $status }}" @selected(($filter->status ?? 'all') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>

            <x-admin.input type="date" name="from" label="From" :value="$filter->from" class="w-full min-h-[44px]" />
            <x-admin.input type="date" name="to" label="To" :value="$filter->to" class="w-full min-h-[44px]" />

            <label class="admin-field">
                <span class="admin-label">Compare</span>
                <select name="compare" class="admin-input w-full min-h-[44px]">
                    @foreach ($report['compare_modes'] as $mode)
                        <option value="{{ $mode->value }}" @selected($filter->compareMode === $mode)>{{ $mode->label() }}</option>
                    @endforeach
                </select>
            </label>

            @if ($filter->compareMode->value === 'branch' || $filter->compareMode->value === 'employee')
                @if ($filter->compareMode->value === 'branch')
                    <label class="admin-field">
                        <span class="admin-label">Compare branch</span>
                        <select name="compare_branch_id" class="admin-input w-full min-h-[44px]">
                            @foreach ($report['branches'] as $branch)
                                <option value="{{ $branch->id }}" @selected($filter->compareBranchId === $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                @if ($filter->compareMode->value === 'employee')
                    <label class="admin-field">
                        <span class="admin-label">Compare employee</span>
                        <select name="compare_employee_id" class="admin-input w-full min-h-[44px]">
                            @foreach ($report['employees'] as $employee)
                                <option value="{{ $employee->id }}" @selected($filter->compareEmployeeId === $employee->id)>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif
            @endif

            <x-admin.button type="submit" class="hidden w-full min-h-[44px] lg:inline-flex">Apply filters</x-admin.button>
        </form>
        <x-slot:footer>
            <button type="submit" form="report-filters-form" class="admin-touch-target inline-flex flex-1 items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white" @click="filterOpen = false">Apply filters</button>
            <a href="{{ route($actionRoute) }}" class="admin-touch-target inline-flex items-center justify-center rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-200">Clear</a>
        </x-slot:footer>
    </x-admin.filter-sheet>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-primary-100 bg-primary-50/60 px-4 py-3 dark:border-primary-900/40 dark:bg-primary-900/20">
        <div class="min-w-0">
            <p class="text-sm font-semibold text-primary-900 dark:text-primary-100">{{ $filter->reportType->label() }} · {{ $report['range']['label'] }}</p>
            <p class="text-xs text-primary-700/80 dark:text-primary-200/80">{{ \Illuminate\Support\Carbon::parse($filter->from)->format('d M Y') }} – {{ \Illuminate\Support\Carbon::parse($filter->to)->format('d M Y') }}</p>
        </div>
        @if ($report['growth'] !== null)
            <span @class([
                'shrink-0 rounded-full px-3 py-1 text-sm font-bold',
                'bg-white text-primary-700' => $report['growth'] >= 0,
                'bg-red-100 text-red-700' => $report['growth'] < 0,
            ])>{{ $report['growth'] >= 0 ? '+' : '' }}{{ $report['growth'] }}% vs previous</span>
        @endif
    </div>

    <x-admin.mobile-kpi-grid :items="array_merge($kpiItems, $kpiSecondary)" class="mb-4" />

    <div class="report-stat-grid admin-stat-grid--compact mb-4 hidden lg:grid">
        @foreach (array_merge($kpiItems, $kpiSecondary) as $item)
            <x-admin.stat-card
                compact
                :label="$item['label']"
                :value="$item['value']"
                :change="$item['change'] ?? null"
                :tone="$item['tone'] ?? 'neutral'"
            />
        @endforeach
    </div>

    @if ($report['comparison'])
        <section class="admin-card mb-4 p-4 lg:mb-6 lg:p-5">
            <h2 class="font-display text-sm font-bold uppercase tracking-wide text-gray-500">Compare mode · {{ $report['comparison']['label'] }}</h2>
            <dl class="mt-3 grid grid-cols-2 gap-2.5 sm:grid-cols-3">
                @foreach (['revenue' => 'Revenue', 'expenses' => 'Expenses', 'profit' => 'Profit'] as $key => $label)
                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-900/50 sm:p-4">
                        <dt class="text-xs font-semibold uppercase text-gray-500">{{ $label }}</dt>
                        <dd class="mt-1 text-lg font-bold text-gray-900 dark:text-white sm:text-xl">£{{ number_format((float) ($report['comparison']['kpis'][$key] ?? 0), 2) }}</dd>
                        <dd class="mt-1 text-xs text-gray-500">Current: £{{ number_format((float) ($kpis[$key] ?? 0), 2) }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    @endif

    @if ($report['insights'] !== [])
        <section class="admin-card mb-4 p-4 lg:mb-6 lg:p-5">
            <h2 class="font-display text-base font-bold text-gray-900 dark:text-white">Business insights</h2>
            <div class="mt-3 grid grid-cols-2 gap-2.5 xl:grid-cols-4">
                @foreach ($report['insights'] as $insight)
                    <article class="rounded-xl border border-gray-100 p-3 dark:border-gray-800 sm:p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $insight['title'] }}</p>
                        <p class="mt-1.5 text-sm font-semibold text-gray-900 dark:text-white">{{ $insight['value'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if ($showCharts)
        <div class="report-chart-grid mb-4 grid gap-4 lg:mb-6 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">
            <x-admin.report-line-chart title="Revenue trend" description="Daily revenue across the selected period." :series="$charts['revenue_trend']" />
            <x-admin.report-line-chart title="Expenses trend" description="Daily expenses across the selected period." :series="$charts['expenses_trend']" />
            <x-admin.report-line-chart title="Profit trend" description="Revenue minus expenses by day." :series="$charts['profit_trend']" />
            <x-admin.report-line-chart title="Attendance trend" description="Staff hours logged per day." :series="$charts['attendance_trend']" format="number" />
            <x-admin.report-line-chart title="Payroll trend" description="Paid payroll by day." :series="$charts['payroll_trend']" />
            <x-admin.report-bar-chart title="Expense categories" description="Where money is going." :items="$charts['expense_categories']" label-key="category" value-key="total" />
            <x-admin.report-bar-chart title="Supplier spend" :items="$charts['supplier_spend']" label-key="supplier" value-key="total" />
            <x-admin.report-bar-chart title="Branch comparison" description="Net cash-up revenue by branch." :items="$charts['branch_comparison']" label-key="branch" value-key="revenue" />
        </div>
    @endif

    <section class="mb-3">
        <h2 class="font-display text-lg font-bold text-gray-900 dark:text-white">{{ $filter->reportType->label() }} detail</h2>
        <p class="mt-1 text-sm text-gray-500">{{ $report['summary']['headline'] ?? 'Total' }}: <span class="font-semibold text-primary-700">{{ $report['summary']['value'] ?? '' }}</span></p>
    </section>

    @if ($report['is_empty'])
        <x-admin.empty-state :title="'No '.$filter->reportType->label().' data'" :description="$report['empty_message']" />
    @else
        <x-admin.report-table :columns="$report['table']['columns']" :rows="$report['table']['rows']" />
    @endif
</div>
