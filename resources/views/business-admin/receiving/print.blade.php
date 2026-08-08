<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>GRN {{ $grn->grn_number }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; color: #111; margin: 24px; }
        h1 { color: #166534; margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 13px; }
        th { background: #f3f4f6; }
        .meta { font-size: 13px; color: #444; }
    </style>
</head>
<body>
    <h1>TotalCashPro</h1>
    <p class="meta">Goods Received Note · {{ $grn->grn_number }}</p>
    <p class="meta">PO: {{ $grn->purchaseOrder?->po_number }} · Supplier: {{ $grn->purchaseOrder?->supplier?->name }}</p>
    <p class="meta">Branch: {{ $grn->purchaseOrder?->branch?->name }} · Date: {{ $grn->received_at?->format('d M Y') }}</p>
    <p class="meta">Received by: {{ $grn->receiver?->name }}</p>

    <table>
        <thead>
            <tr><th>Description</th><th>Ordered</th><th>Received</th><th>Damaged</th><th>Missing</th><th>Accepted</th></tr>
        </thead>
        <tbody>
            @foreach ($grn->lines as $line)
                <tr>
                    <td>{{ $line->purchaseOrderLine?->description }}</td>
                    <td>{{ number_format((float) ($line->purchaseOrderLine?->quantity ?? 0), 2) }}</td>
                    <td>{{ number_format((float) $line->quantity_received, 2) }}</td>
                    <td>{{ number_format((float) $line->quantity_damaged, 2) }}</td>
                    <td>{{ number_format((float) $line->quantity_missing, 2) }}</td>
                    <td>{{ number_format((float) $line->quantity_accepted, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($grn->notes)
        <p class="meta" style="margin-top:16px"><strong>Notes:</strong> {{ $grn->notes }}</p>
    @endif
</body>
</html>
