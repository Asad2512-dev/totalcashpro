<x-layouts.business-admin title="Finance Reports" active="finance">
    <x-finance.nav active="reports" />

    <x-reports.center
        :report="$report"
        :filter="$filter"
        action-route="business-admin.finance.reports"
        export-route="business-admin.finance.reports.export"
        :show-charts="false"
        toolbar-title="Finance Reports"
        toolbar-description="Full analytics for cash flow, payroll, expenses and more — same powerful engine as Reports Center."
    />
</x-layouts.business-admin>
