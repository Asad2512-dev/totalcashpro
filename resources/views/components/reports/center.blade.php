@props([
    'report',
    'filter',
    'actionRoute',
    'exportRoute',
    'showToolbar' => true,
    'showCharts' => true,
    'toolbarTitle' => 'Reports',
    'toolbarDescription' => 'Enterprise analytics for your business — one interface, every insight.',
])

@php
    $kpis = $report['kpis'];
    $charts = $report['charts'];
    $query = $filter->toQueryArray();
@endphp

<div class="report-center print:text-black" x-data="{ loadedAt: Date.now() }" x-init="document.addEventListener('visibilitychange', () => { if (document.visibilityState === 'visible' && Date.now() - loadedAt > 45000) { window.location.reload(); } })">
    @if ($showToolbar)
    <x-admin.toolbar :description="$toolbarDescription">
            <div class="flex flex-wrap items-center gap-2 print:hidden">
                @if (! empty($report['generated_at']))
                    <span class="hidden text-xs text-gray-400 lg:inline" title="Reports refresh automatically when your data changes">
                        Live · {{ \Illuminate\Support\Carbon::parse($report['generated_at'])->format('H:i') }}
                    </span>
                @endif
                <a href="{{ route($exportRoute, array_merge($query, ['format' => 'csv'])) }}" class="inline-flex items-center rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200">CSV</a>
                <a href="{{ route($exportRoute, array_merge($query, ['format' => 'excel'])) }}" class="inline-flex items-center rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200">Excel</a>
                <button type="button" onclick="window.print()" class="inline-flex items-center rounded-xl bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700">Print</button>
            </div>
        </x-admin.toolbar>
    @endif

    <form method="GET" action="{{ route($actionRoute) }}" class="report-filters admin-card mb-6 space-y-4 p-4 print:hidden">
        <div class="grid gap-4 xl:grid-cols-5">
            <label class="block space-y-1.5">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Date range</span>
                <select name="date_preset" class="admin-input w-full" onchange="this.form.submit()">
                    @foreach ($report['date_presets'] as $preset)
                        <option value="{{ $preset->value }}" @selected($filter->datePreset === $preset)>{{ $preset->label() }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block space-y-1.5">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Branch</span>
                <select name="branch_id" class="admin-input w-full">
                    <option value="all" @selected($filter->branchId === null)>All branches</option>
                    @foreach ($report['branches'] as $branch)
                        <option value="{{ $branch->id }}" @selected($filter->branchId === $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block space-y-1.5">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Report type</span>
                <select name="report_type" class="admin-input w-full">
                    @foreach ($report['report_types'] as $type)
                        <option value="{{ $type->value }}" @selected($filter->reportType === $type)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block space-y-1.5">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Employee</span>
                <select name="employee_id" class="admin-input w-full">
                    <option value="all">All employees</option>
                    @foreach ($report['employees'] as $employee)
                        <option value="{{ $employee->id }}" @selected($filter->employeeId === $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </label>

            <label class="block space-y-1.5">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status</span>
                <select name="status" class="admin-input w-full">
                    @foreach ($report['statuses'] as $status)
                        <option value="{{ $status }}" @selected(($filter->status ?? 'all') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <div class="grid gap-4 lg:grid-cols-[1fr_1fr_1fr_auto]">
            <x-admin.input type="date" name="from" label="From" :value="$filter->from" class="w-full" />
            <x-admin.input type="date" name="to" label="To" :value="$filter->to" class="w-full" />

            <label class="block space-y-1.5">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Compare</span>
                <select name="compare" class="admin-input w-full">
                    @foreach ($report['compare_modes'] as $mode)
                        <option value="{{ $mode->value }}" @selected($filter->compareMode === $mode)>{{ $mode->label() }}</option>
                    @endforeach
                </select>
            </label>

            <div class="flex items-end">
                <x-admin.button type="submit" class="w-full lg:w-auto">Apply filters</x-admin.button>
            </div>
        </div>

        @if ($filter->compareMode->value === 'branch')
            <label class="block max-w-sm space-y-1.5">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Compare branch</span>
                <select name="compare_branch_id" class="admin-input w-full">
                    @foreach ($report['branches'] as $branch)
                        <option value="{{ $branch->id }}" @selected($filter->compareBranchId === $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </label>
        @endif

        @if ($filter->compareMode->value === 'employee')
            <label class="block max-w-sm space-y-1.5">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Compare employee</span>
                <select name="compare_employee_id" class="admin-input w-full">
                    @foreach ($report['employees'] as $employee)
                        <option value="{{ $employee->id }}" @selected($filter->compareEmployeeId === $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </label>
        @endif
    </form>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-primary-100 bg-primary-50/60 px-4 py-3 dark:border-primary-900/40 dark:bg-primary-900/20">
        <div>
            <p class="text-sm font-semibold text-primary-900 dark:text-primary-100">{{ $filter->reportType->label() }} · {{ $report['range']['label'] }}</p>
            <p class="text-xs text-primary-700/80 dark:text-primary-200/80">{{ \Illuminate\Support\Carbon::parse($filter->from)->format('d M Y') }} – {{ \Illuminate\Support\Carbon::parse($filter->to)->format('d M Y') }}</p>
        </div>
        @if ($report['growth'] !== null)
            <span @class([
                'rounded-full px-3 py-1 text-sm font-bold',
                'bg-white text-primary-700' => $report['growth'] >= 0,
                'bg-red-100 text-red-700' => $report['growth'] < 0,
            ])>{{ $report['growth'] >= 0 ? '+' : '' }}{{ $report['growth'] }}% vs previous</span>
        @endif
    </div>

    @if ($report['comparison'])
        <section class="admin-card mb-6 p-5">
            <h2 class="font-display text-sm font-bold uppercase tracking-wide text-gray-500">Compare mode · {{ $report['comparison']['label'] }}</h2>
            <dl class="mt-4 grid gap-4 sm:grid-cols-3">
                @foreach (['revenue' => 'Revenue', 'expenses' => 'Expenses', 'profit' => 'Profit'] as $key => $label)
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900/50">
                        <dt class="text-xs font-semibold uppercase text-gray-500">{{ $label }}</dt>
                        <dd class="mt-1 text-xl font-bold text-gray-900 dark:text-white">£{{ number_format((float) ($report['comparison']['kpis'][$key] ?? 0), 2) }}</dd>
                        <dd class="mt-1 text-xs text-gray-500">Current: £{{ number_format((float) ($kpis[$key] ?? 0), 2) }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    @endif

    <div class="report-stat-grid mb-6">
        <x-admin.stat-card label="Revenue" :value="'£'.number_format((float) $kpis['revenue'], 2)" :change="'Avg £'.number_format((float) $report['averages']['daily_revenue'], 2).'/day'" tone="success" />
        <x-admin.stat-card label="Expenses" :value="'£'.number_format((float) $kpis['expenses'], 2)" change="Period total" tone="warning" />
        <x-admin.stat-card label="Profit" :value="'£'.number_format((float) $kpis['profit'], 2)" :tone="$kpis['profit'] >= 0 ? 'success' : 'danger'" change="Estimated" />
        <x-admin.stat-card label="Cash (net)" :value="'£'.number_format((float) $kpis['cash'], 2)" change="Cash ups" tone="info" />
        <x-admin.stat-card label="Cards" :value="'£'.number_format((float) $kpis['cards'], 2)" change="Card takings" tone="neutral" />
        <x-admin.stat-card label="Online orders" :value="'£'.number_format((float) $kpis['online_orders'], 2)" change="Delivery apps" tone="info" />
        <x-admin.stat-card label="Bills due" :value="'£'.number_format((float) $kpis['bills'], 2)" change="Outstanding" tone="danger" />
        <x-admin.stat-card label="Payroll" :value="'£'.number_format((float) $kpis['payroll'], 2)" :change="'£'.number_format((float) $kpis['payroll_pending'], 2).' pending'" tone="warning" />
        <x-admin.stat-card label="Supplier spend" :value="'£'.number_format((float) $kpis['supplier_payments'], 2)" change="Paid out" tone="neutral" />
        <x-admin.stat-card label="Staff hours" :value="number_format((float) $kpis['attendance_hours'], 1).'h'" change="Clocked time" tone="info" />
        <x-admin.stat-card label="Avg weekly revenue" :value="'£'.number_format((float) $report['averages']['weekly_revenue'], 2)" change="Period average" tone="success" />
        <x-admin.stat-card label="Low stock items" :value="(string) $kpis['low_stock']" change="Needs attention" tone="danger" />
    </div>

    @if ($report['insights'] !== [])
        <section class="admin-card mb-6 p-5">
            <h2 class="font-display text-base font-bold text-gray-900 dark:text-white">Business insights</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($report['insights'] as $insight)
                    <article class="rounded-xl border border-gray-100 p-4 dark:border-gray-800">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $insight['title'] }}</p>
                        <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $insight['value'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if ($showCharts)
        <div class="mb-6 grid gap-6 xl:grid-cols-2">
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

    <section class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="font-display text-lg font-bold text-gray-900 dark:text-white">{{ $filter->reportType->label() }} detail</h2>
            <p class="text-sm text-gray-500">{{ $report['summary']['headline'] ?? 'Total' }}: <span class="font-semibold text-primary-700">{{ $report['summary']['value'] ?? '' }}</span></p>
        </div>
    </section>

    @if ($report['is_empty'])
        <x-admin.empty-state :title="'No '.$filter->reportType->label().' data'" :description="$report['empty_message']" />
    @else
        <x-admin.report-table :columns="$report['table']['columns']" :rows="$report['table']['rows']" />
    @endif
</div>
