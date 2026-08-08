<x-layouts.business-admin title="Cash Up Detail" active="cash-history">
    <x-admin.toolbar :title="$cashUp->branch?->name.' · '.($cashUp->cashDrawer?->name ?? 'Till')" :description="$cashUp->cashup_date?->format('l, d M Y').' · '.($cashUp->shift?->value ?? $cashUp->shift)">
        <a href="{{ route('business-admin.cash-history') }}" class="text-sm font-semibold text-primary-700 hover:underline">← History</a>
        <a href="{{ route('business-admin.cash-up.print', $cashUp) }}" target="_blank" class="text-sm font-semibold text-primary-700 hover:underline">Print</a>
    </x-admin.toolbar>

    <div class="mb-4 flex flex-wrap gap-2">
        @if (! $cashUp->isLocked())
            <form method="POST" action="{{ route('business-admin.cash-up.submit', $cashUp) }}">@csrf<button class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white">Submit</button></form>
        @endif
        @if (($cashUp->status?->value ?? $cashUp->status) === 'submitted')
            <form method="POST" action="{{ route('business-admin.cash-up.approve', $cashUp) }}">@csrf<button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Approve</button></form>
            <form method="POST" action="{{ route('business-admin.cash-up.reject', $cashUp) }}">@csrf<button class="rounded-xl border px-4 py-2 text-sm font-semibold">Reject</button></form>
        @endif
        @if ($cashUp->isLocked())
            <form method="POST" action="{{ route('business-admin.cash-up.reopen', $cashUp) }}">@csrf<button class="rounded-xl border px-4 py-2 text-sm font-semibold">Reopen</button></form>
        @endif
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <x-admin.card title="Reconciliation">
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-gray-500">Opening float</dt><dd class="font-semibold">£{{ number_format($cashUp->effectiveOpeningFloat(), 2) }}</dd></div>
                <div><dt class="text-gray-500">Cash sales</dt><dd class="font-semibold">£{{ number_format($cashUp->cashSalesTotal(), 2) }}</dd></div>
                <div><dt class="text-gray-500">Cash expenses</dt><dd class="font-semibold">£{{ number_format((float) $cashUp->expenses_total, 2) }}</dd></div>
                <div><dt class="text-gray-500">Expected cash</dt><dd class="font-semibold">£{{ number_format($cashUp->calculatedExpectedCash(), 2) }}</dd></div>
                <div><dt class="text-gray-500">Actual cash</dt><dd class="font-semibold">£{{ number_format($cashUp->physicalCashTotal(), 2) }}</dd></div>
                <div><dt class="text-gray-500">Variance</dt><dd class="font-semibold">£{{ number_format($cashUp->varianceAmount(), 2) }}</dd></div>
                <div><dt class="text-gray-500">Revenue (P&amp;L)</dt><dd class="font-semibold">£{{ number_format($cashUp->revenueTotal(), 2) }}</dd></div>
                <div><dt class="text-gray-500">Status</dt><dd class="font-semibold">{{ $cashUp->status?->label() ?? $cashUp->status }}</dd></div>
            </dl>
        </x-admin.card>

        <x-admin.card title="Other takings">
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-gray-500">Cards</dt><dd class="font-semibold">£{{ number_format((float) $cashUp->cards_total, 2) }}</dd></div>
                <div><dt class="text-gray-500">Online</dt><dd class="font-semibold">£{{ number_format((float) $cashUp->online_orders_total, 2) }}</dd></div>
                <div><dt class="text-gray-500">Platform deductions</dt><dd class="font-semibold">£{{ number_format((float) $cashUp->platform_deductions_total, 2) }}</dd></div>
                <div><dt class="text-gray-500">Staff</dt><dd class="font-semibold">{{ $cashUp->creator?->name ?? '—' }}</dd></div>
            </dl>
        </x-admin.card>
    </div>

    @if ($cashUp->variance_reason)
        <x-admin.card class="mt-4" title="Variance reason">
            <p class="text-sm text-gray-700 dark:text-gray-200">{{ $cashUp->variance_reason }}</p>
        </x-admin.card>
    @endif
</x-layouts.business-admin>
