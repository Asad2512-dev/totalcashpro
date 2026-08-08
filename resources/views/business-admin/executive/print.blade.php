<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Executive Report · {{ $organization?->name }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; margin: 24px; color: #111; }
        h1 { color: #166534; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 13px; }
        th { background: #f3f4f6; }
        .meta { color: #555; font-size: 13px; }
    </style>
</head>
<body>
    <h1>TotalCashPro Executive Report</h1>
    <p class="meta">{{ $organization?->name }} · {{ $data['range']['label'] }} · Generated {{ now()->format('d M Y H:i') }}</p>

    <h2>Key metrics</h2>
    <table>
        <thead><tr><th>KPI</th><th>Current</th><th>Previous</th><th>Change</th></tr></thead>
        <tbody>
            @foreach ($data['kpis'] as $key => $kpi)
                <tr>
                    <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                    <td>{{ $key === 'gross_margin' ? $kpi['current'].'%' : '£'.number_format((float) $kpi['current'], 2) }}</td>
                    <td>{{ $key === 'gross_margin' ? $kpi['previous'].'%' : '£'.number_format((float) $kpi['previous'], 2) }}</td>
                    <td>{{ $kpi['percent'] !== null ? $kpi['percent'].'%' : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (count($data['alerts']) > 0)
        <h2>Alerts</h2>
        <ul>
            @foreach ($data['alerts'] as $alert)
                <li>{{ $alert->title }} — {{ $alert->message }}</li>
            @endforeach
        </ul>
    @endif
</body>
</html>
