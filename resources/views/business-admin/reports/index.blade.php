<x-layouts.business-admin title="Reports Center" active="reports">
    <x-reports.center
        :report="$report"
        :filter="$filter"
        action-route="business-admin.reports"
        export-route="business-admin.reports.export"
        toolbar-title="Reports Center"
        toolbar-description="Enterprise analytics for your business — one interface, every insight."
    />

    @if (! empty($savedReports) && $savedReports->isNotEmpty())
        <x-admin.card class="mt-6">
            <h3 class="font-display text-lg font-bold text-gray-900 dark:text-white">Saved reports</h3>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($savedReports as $saved)
                    <li class="flex items-center justify-between rounded-xl border border-gray-100 px-3 py-2 dark:border-gray-700">
                        <span>{{ $saved->name }} · {{ ucfirst(str_replace('_', ' ', $saved->report_type)) }}</span>
                        <x-admin.table-action :href="route('business-admin.reports', array_merge($saved->filters ?? [], ['report_type' => $saved->report_type]))" variant="neutral">Load</x-admin.table-action>
                    </li>
                @endforeach
            </ul>
            <form method="POST" action="{{ route('business-admin.reports.saved') }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <input type="hidden" name="report_type" value="{{ $filter->reportType->value }}">
                <input type="hidden" name="from" value="{{ $filter->from }}">
                <input type="hidden" name="to" value="{{ $filter->to }}">
                @if ($filter->branchId)
                    <input type="hidden" name="branch_id" value="{{ $filter->branchId }}">
                @endif
                <x-admin.input name="name" label="Save current view as" placeholder="Monthly overview" class="flex-1" />
                <x-admin.button type="submit" size="sm">Save report</x-admin.button>
            </form>
        </x-admin.card>
    @else
        <x-admin.card class="mt-6">
            <form method="POST" action="{{ route('business-admin.reports.saved') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <input type="hidden" name="report_type" value="{{ $filter->reportType->value }}">
                <input type="hidden" name="from" value="{{ $filter->from }}">
                <input type="hidden" name="to" value="{{ $filter->to }}">
                @if ($filter->branchId)
                    <input type="hidden" name="branch_id" value="{{ $filter->branchId }}">
                @endif
                <x-admin.input name="name" label="Save current report" placeholder="My report name" class="flex-1" />
                <x-admin.button type="submit" size="sm">Save</x-admin.button>
            </form>
        </x-admin.card>
    @endif
</x-layouts.business-admin>
