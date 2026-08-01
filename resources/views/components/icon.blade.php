@props(['name' => 'cash'])

@php
    $paths = [
        'cash' => '<path d="M4 8.5h16v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-9Z"/><path d="M4 8.5 12 4l8 4.5"/><circle cx="12" cy="13" r="2.2"/>',
        'attendance' => '<path d="M8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M3.5 19c.6-2.8 2.5-4.2 4.5-4.2s3.9 1.4 4.5 4.2"/><path d="M15 8h5.5M17.8 5.2v5.6"/>',
        'clock-in' => '<circle cx="12" cy="12" r="8"/><path d="M12 8v4.5l2.5 1.5"/><path d="M5 12H3.5M7 6.5 5.8 5.3"/>',
        'clock-out' => '<circle cx="12" cy="12" r="8"/><path d="M12 8v4.5l2.5 1.5"/><path d="M19 12h1.5M17 6.5l1.2-1.2"/>',
        'payroll' => '<rect x="4" y="5" width="16" height="14" rx="2"/><path d="M8 9h8M8 13h5"/><circle cx="16.5" cy="13" r="1.2"/>',
        'inventory' => '<path d="M4.5 8.5 12 4.5l7.5 4v7l-7.5 4-7.5-4v-7Z"/><path d="M12 12.5v7M4.5 8.5 12 12.5l7.5-4"/>',
        'suppliers' => '<path d="M4 17.5h16"/><path d="M6 17.5V9.5l4-3h4l4 3v8"/><path d="M9 17.5v-4h6v4"/>',
        'reports' => '<path d="M6 18V9M11 18V6M16 18v-7M4 18h16"/>',
        'analytics' => '<path d="M4 16l4.5-4.5 3.5 3L20 7"/><path d="M14.5 7H20v5.5"/>',
        'roles' => '<path d="M8.5 10.5a2.8 2.8 0 1 0 0-5.6 2.8 2.8 0 0 0 0 5.6Z"/><path d="M15.8 11.2a2.3 2.3 0 1 0 0-4.6 2.3 2.3 0 0 0 0 4.6Z"/><path d="M3.8 18.5c.7-2.4 2.4-3.6 4.7-3.6s4 1.2 4.7 3.6"/><path d="M13.2 15.2c1.5-.4 2.8.1 3.7 1.5.4.6.7 1.2.8 1.8"/>',
        'branches' => '<path d="M12 4v6"/><path d="M7 20v-5a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v5"/><circle cx="12" cy="4" r="2"/><circle cx="7" cy="20" r="2"/><circle cx="17" cy="20" r="2"/>',
        'security' => '<path d="M12 3.5 19 6.5v5.2c0 4.4-2.9 7.4-7 8.8-4.1-1.4-7-4.4-7-8.8V6.5L12 3.5Z"/><path d="M10 12.2l1.5 1.5L14.5 10"/>',
        'cloud' => '<path d="M7.5 17.5h9.2a3.8 3.8 0 0 0 .4-7.6 5 5 0 0 0-9.5 1.4A3.4 3.4 0 0 0 7.5 17.5Z"/>',
        'fast' => '<path d="M13 4 6 13.5h5L10 20l8-11h-5L13 4Z"/>',
        'reliable' => '<path d="M12 4v3M12 17v3M4 12h3M17 12h3"/><circle cx="12" cy="12" r="4.5"/>',
        'easy' => '<path d="M8 12.5 10.5 15 16 9"/><rect x="4" y="4" width="16" height="16" rx="4"/>',
        'scalable' => '<path d="M5 16V8M9.5 16V5.5M14 16V9.5M18.5 16V4.5"/>',
        'affordable' => '<circle cx="12" cy="12" r="8"/><path d="M12 7.5v9M9.5 9.8c.5-1 1.4-1.5 2.5-1.5 1.4 0 2.5.8 2.5 2.1S13.8 12.5 12 12.5 9.5 13.3 9.5 14.6c0 1.2 1.1 2 2.5 2 1.1 0 2-.5 2.5-1.5"/>',
    ];
@endphp

<svg
    {{ $attributes->merge(['class' => 'h-5 w-5']) }}
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.7"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
>
    {!! $paths[$name] ?? $paths['cash'] !!}
</svg>
