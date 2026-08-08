<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Cash Up · {{ $cashUp->cashup_date?->format('d M Y') }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; color: #111; margin: 24px; }
        h1 { margin: 0 0 4px; font-size: 22px; }
        .muted { color: #666; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #ddd; padding: 8px 6px; text-align: left; font-size: 13px; }
        th { background: #f8fafc; }
        .totals { margin-top: 16px; max-width: 360px; margin-left: auto; }
        .totals div { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #eee; }
        .brand { margin-top: 28px; font-size: 12px; color: #888; }
        @media print { body { margin: 12mm; } }
    </style>
</head>
<body onload="window.print()">
    <h1>Daily Cash Up</h1>
    <p class="muted">{{ $cashUp->organization?->name ?? 'TotalCashPro' }} · {{ $cashUp->branch?->name }} · {{ $cashUp->cashDrawer?->name ?? 'Till' }}</p>
    <p class="muted">{{ $cashUp->cashup_date?->format('l, d F Y') }} · {{ $cashUp->shift?->value ?? $cashUp->shift }} · {{ $cashUp->creator?->name }}</p>

    <table>
        <thead><tr><th>Item</th><th>Amount</th></tr></thead>
        <tbody>
            <tr><td>Opening float</td><td>£{{ number_format($cashUp->effectiveOpeningFloat(), 2) }}</td></tr>
            <tr><td>Cash sales</td><td>£{{ number_format($cashUp->cashSalesTotal(), 2) }}</td></tr>
            <tr><td>Cash expenses</td><td>£{{ number_format((float) $cashUp->expenses_total, 2) }}</td></tr>
            <tr><td>Cards</td><td>£{{ number_format((float) $cashUp->cards_total, 2) }}</td></tr>
            <tr><td>Online orders</td><td>£{{ number_format((float) $cashUp->online_orders_total, 2) }}</td></tr>
            <tr><td>Platform deductions</td><td>£{{ number_format((float) $cashUp->platform_deductions_total, 2) }}</td></tr>
        </tbody>
    </table>

    <div class="totals">
        <div><span>Expected cash</span><strong>£{{ number_format($cashUp->calculatedExpectedCash(), 2) }}</strong></div>
        <div><span>Actual cash</span><strong>£{{ number_format($cashUp->physicalCashTotal(), 2) }}</strong></div>
        <div><span>Variance</span><strong>£{{ number_format($cashUp->varianceAmount(), 2) }}</strong></div>
        <div><span>Revenue (excl. float)</span><strong>£{{ number_format($cashUp->revenueTotal(), 2) }}</strong></div>
    </div>

    @if ($cashUp->variance_reason)
        <p><strong>Variance reason:</strong> {{ $cashUp->variance_reason }}</p>
    @endif

    <p class="brand">TotalCashPro · Printed {{ now()->format('d M Y H:i') }}</p>
</body>
</html>
