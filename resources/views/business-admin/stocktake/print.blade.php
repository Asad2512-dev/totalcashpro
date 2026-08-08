<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Stocktake · {{ $stocktake->week_start?->format('d M Y') }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; margin: 24px; color: #111; }
        h1 { margin: 0 0 4px; font-size: 22px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border-bottom: 1px solid #ddd; padding: 8px 6px; font-size: 12px; text-align: left; }
        th { background: #f8fafc; }
    </style>
</head>
<body onload="window.print()">
    <h1>Weekly Stocktake</h1>
    <p>{{ $stocktake->branch?->name }} · {{ $stocktake->week_start?->format('d M') }} – {{ $stocktake->week_end?->format('d M Y') }}</p>
    <p>Staff: {{ $stocktake->creator?->name }}</p>
    <table>
        <thead>
            <tr>
                <th>Item</th><th>Category</th><th>System</th><th>Counted</th><th>Par</th><th>Suggested</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stocktake->items as $line)
                <tr>
                    <td>{{ $line->item?->name }}</td>
                    <td>{{ $line->item?->category?->name }}</td>
                    <td>{{ number_format((float) $line->system_qty, 1) }}</td>
                    <td>{{ $line->counted_qty !== null ? number_format((float) $line->counted_qty, 1) : '' }}</td>
                    <td>{{ number_format((float) $line->par_level, 1) }}</td>
                    <td>{{ number_format((float) $line->suggested_order_qty, 1) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
