<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Rota — {{ $weekLabel }}</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, sans-serif; color: #111827; background: #fff; }
        .print-wrap { max-width: 1100px; margin: 0 auto; padding: 24px; }
        .print-header { text-align: center; border-bottom: 3px solid #16a34a; padding-bottom: 16px; margin-bottom: 20px; }
        .print-brand { font-size: 11px; letter-spacing: 0.2em; font-weight: 700; color: #16a34a; }
        .print-org { font-size: 22px; font-weight: 800; margin-top: 8px; text-transform: uppercase; }
        .print-branch { font-size: 14px; color: #4b5563; margin-top: 4px; }
        .print-title { font-size: 16px; font-weight: 700; margin-top: 12px; }
        .print-meta { font-size: 12px; color: #6b7280; margin-top: 6px; }
        .print-status { display: inline-block; margin-top: 8px; padding: 4px 10px; border: 1px solid #16a34a; color: #166534; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #d1d5db; padding: 8px 6px; vertical-align: top; }
        th { background: #f3f4f6; font-weight: 700; text-align: center; }
        .staff-name { font-weight: 700; white-space: nowrap; background: #fafafa; }
        .shift-time { font-weight: 700; color: #111827; }
        .shift-section { color: #4b5563; font-size: 10px; margin-top: 2px; }
        .off { color: #9ca3af; text-align: center; }
        .summary { margin-top: 20px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
        .summary-item { border: 1px solid #e5e7eb; padding: 10px; }
        .summary-label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.08em; }
        .summary-value { font-size: 18px; font-weight: 800; margin-top: 4px; }
        .print-footer { margin-top: 24px; font-size: 10px; color: #6b7280; text-align: center; }
        @media print {
            .no-print { display: none !important; }
            .print-wrap { max-width: none; padding: 0; }
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
        }
        @page { size: A4 landscape; margin: 12mm; }
    </style>
</head>
<body>
    <div class="print-wrap">
        <div class="no-print" style="margin-bottom: 16px;">
            <button onclick="window.print()" style="background:#16a34a;color:#fff;border:0;padding:10px 16px;border-radius:8px;font-weight:700;cursor:pointer;">Print Rota</button>
        </div>

        <header class="print-header">
            <div class="print-brand">{{ brand_name() }}</div>
            <div class="print-org">{{ $organization?->name }}</div>
            <div class="print-branch">{{ $branch?->name }}</div>
            <div class="print-title">Staff Weekly Rota</div>
            <div class="print-meta">{{ strtoupper($weekLabel) }}</div>
            <div class="print-status">{{ $statusLabel }}</div>
            <div class="print-meta">Generated {{ $generatedAt->format('d M Y H:i') }} · Prepared by {{ $preparedBy }}</div>
        </header>

        <table>
            <thead>
                <tr>
                    <th style="text-align:left;">Staff</th>
                    @foreach ($days as $day)
                        <th>{{ $day['label'] }}<br><span style="font-weight:500;color:#6b7280;">{{ $day['full'] }}</span></th>
                    @endforeach
                    <th>Hours</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td class="staff-name">{{ $row['name'] }}</td>
                        @foreach ($row['cells'] as $cell)
                            <td>
                                @if ($cell === null)
                                    <div class="off">OFF</div>
                                @else
                                    @foreach ($cell as $shift)
                                        <div style="margin-bottom:6px;">
                                            <div class="shift-time">{{ $shift['time'] }}</div>
                                            <div class="shift-section">{{ $shift['section'] ?? $shift['type'] }}</div>
                                        </div>
                                    @endforeach
                                @endif
                            </td>
                        @endforeach
                        <td style="text-align:center;font-weight:700;">{{ $row['total_hours'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($days) + 2 }}">No shifts scheduled.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-item"><div class="summary-label">Staff</div><div class="summary-value">{{ $summary['staff_count'] }}</div></div>
            <div class="summary-item"><div class="summary-label">Shifts</div><div class="summary-value">{{ $summary['shift_count'] }}</div></div>
            <div class="summary-item"><div class="summary-label">Total hours</div><div class="summary-value">{{ $summary['total_hours'] }}</div></div>
            <div class="summary-item"><div class="summary-label">Version</div><div class="summary-value">v{{ $version->version_number }}</div></div>
        </div>

        <footer class="print-footer">
            {{ $branch?->name }} · Published version {{ $version->version_number }} · {{ brand_name() }}
        </footer>
    </div>
</body>
</html>
