<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Rota — {{ $weekLabel }}</title>
    <style>
        :root { color-scheme: light; }
        body { margin: 0; font-family: ui-sans-serif, system-ui, sans-serif; color: #111827; background: #fff; }
        .wrap { max-width: 700px; margin: 0 auto; padding: 24px; }
        .header { border-bottom: 3px solid #16a34a; padding-bottom: 14px; margin-bottom: 18px; }
        .brand { font-size: 11px; letter-spacing: 0.18em; color: #16a34a; font-weight: 700; }
        .name { font-size: 24px; font-weight: 800; margin-top: 8px; text-transform: uppercase; }
        .meta { color: #6b7280; font-size: 13px; margin-top: 4px; }
        .day { border: 1px solid #e5e7eb; padding: 12px; margin-bottom: 10px; page-break-inside: avoid; }
        .day-title { font-weight: 800; font-size: 14px; text-transform: uppercase; }
        .off { color: #9ca3af; font-weight: 700; margin-top: 6px; }
        .shift { margin-top: 8px; }
        .time { font-size: 16px; font-weight: 800; }
        .detail { font-size: 12px; color: #4b5563; margin-top: 2px; }
        .summary { margin-top: 18px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .summary-item { border: 1px solid #e5e7eb; padding: 10px; }
        .no-print button { background:#16a34a;color:#fff;border:0;padding:10px 16px;border-radius:8px;font-weight:700;cursor:pointer;margin-bottom:16px; }
        @media print { .no-print { display:none !important; } }
        @page { size: A4 portrait; margin: 14mm; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="no-print">
            <button type="button" onclick="window.print()">Print My Rota</button>
        </div>

        <header class="header">
            <div class="brand">{{ brand_name() }}</div>
            <div class="name">{{ $staff->name }}</div>
            <div class="meta">{{ $staff->branch?->name }} · {{ $weekLabel }}</div>
            <div class="meta">Generated {{ $generatedAt->format('d M Y H:i') }}</div>
        </header>

        @foreach ($days as $day)
            <section class="day">
                <div class="day-title">{{ $day['label'] }} · {{ $day['date'] }}</div>
                @if ($day['off'])
                    <div class="off">OFF</div>
                @else
                    @foreach ($day['shifts'] as $shift)
                        <div class="shift">
                            <div class="time">{{ $shift['time'] }}</div>
                            <div class="detail">{{ $shift['section'] ?? 'Section' }}@if($shift['group']) · {{ $shift['group'] }}@endif</div>
                            @if ($shift['break'])
                                <div class="detail">Break: {{ $shift['break'] }} min</div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </section>
        @endforeach

        <div class="summary">
            <div class="summary-item"><strong>{{ $summary['shift_count'] }}</strong> shifts</div>
            <div class="summary-item"><strong>{{ $summary['total_hours'] }}</strong> scheduled hours</div>
        </div>
    </div>
</body>
</html>
