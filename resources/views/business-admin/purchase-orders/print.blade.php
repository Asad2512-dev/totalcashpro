<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PO {{ $order->po_number }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; margin: 24px; color: #111; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border-bottom: 1px solid #ddd; padding: 8px; font-size: 12px; text-align: left; }
        th { background: #f8fafc; }
    </style>
</head>
<body onload="window.print()">
    <h1>Purchase Order {{ $order->po_number }}</h1>
    <p>{{ $order->branch?->name }} · {{ $order->supplier?->name }}</p>
    <p>Date: {{ $order->created_at?->format('d M Y') }} · Expected: {{ $order->expected_at?->format('d M Y') ?? '—' }}</p>
    <table>
        <thead><tr><th>Description</th><th>Qty</th><th>Unit</th><th>Unit price</th><th>Total</th></tr></thead>
        <tbody>
            @foreach ($order->lines as $line)
                <tr>
                    <td>{{ $line->description }}</td>
                    <td>{{ number_format((float) $line->quantity, 2) }}</td>
                    <td>pcs</td>
                    <td>£{{ number_format((float) $line->unit_cost, 2) }}</td>
                    <td>£{{ number_format((float) $line->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="mt-4 text-right font-bold">Total: £{{ number_format((float) $order->total, 2) }}</p>
</body>
</html>
