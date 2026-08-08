<!DOCTYPE html>
<html lang="en" class="admin-panel">
<head>
    <meta charset="utf-8">
    <x-theme-init />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rider · {{ brand_name() }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
    <div class="mx-auto min-h-screen max-w-lg p-4 pb-24">
        <header class="mb-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-primary-600">Rider</p>
            <h1 class="font-display text-2xl font-bold">Today's deliveries</h1>
            <p class="text-sm text-gray-500">{{ $rider->user?->name }}</p>
        </header>

        <div class="mb-4 grid grid-cols-3 gap-2 text-center text-xs">
            <div class="rounded-xl border p-2 dark:border-gray-700"><p class="text-gray-500">Assigned</p><p class="text-lg font-bold">{{ $stats['assigned'] ?? 0 }}</p></div>
            <div class="rounded-xl border p-2 dark:border-gray-700"><p class="text-gray-500">In transit</p><p class="text-lg font-bold">{{ $stats['in_transit'] ?? 0 }}</p></div>
            <div class="rounded-xl border p-2 dark:border-gray-700"><p class="text-gray-500">Delivered</p><p class="text-lg font-bold">{{ $stats['delivered'] ?? 0 }}</p></div>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-xl bg-primary-50 px-4 py-3 text-sm text-primary-800 dark:bg-primary-950 dark:text-primary-200">{{ session('status') }}</div>
        @endif

        @forelse ($deliveries as $delivery)
            <article class="mb-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">PO #{{ $delivery->purchaseOrder?->po_number }}</p>
                        <p class="text-sm text-gray-500">{{ $delivery->purchaseOrder?->supplier?->name }}</p>
                        <p class="text-sm text-gray-500">Deliver to: {{ $delivery->branch?->name }}</p>
                    </div>
                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold dark:bg-gray-800">{{ $delivery->status?->label() ?? $delivery->status }}</span>
                </div>

                @php
                    $next = match ($delivery->status?->value ?? $delivery->status) {
                        'assigned' => 'accepted',
                        'accepted' => 'at_supplier',
                        'at_supplier' => 'collected',
                        'collected' => 'out_for_delivery',
                        'out_for_delivery' => 'arrived',
                        'arrived' => 'delivered',
                        default => null,
                    };
                @endphp

                @if (($delivery->status?->value ?? $delivery->status) === 'assigned')
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <form method="POST" action="{{ route('rider.deliveries.accept', $delivery) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white">Accept</button>
                        </form>
                        <form method="POST" action="{{ route('rider.deliveries.reject', $delivery) }}">
                            @csrf
                            <input type="hidden" name="reason" value="Unable to take delivery">
                            <button type="submit" class="w-full rounded-xl border px-4 py-3 text-sm font-bold">Reject</button>
                        </form>
                    </div>
                @elseif ($next)
                    <form method="POST" action="{{ route('rider.deliveries.advance', $delivery) }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="status" value="{{ $next }}">
                        <button type="submit" class="w-full rounded-xl bg-primary-600 px-4 py-3 text-sm font-bold text-white">
                            {{ ucfirst(str_replace('_', ' ', $next)) }}
                        </button>
                    </form>
                @endif
            </article>
        @empty
            <p class="rounded-2xl border border-dashed border-gray-300 p-8 text-center text-gray-500">No deliveries assigned today.</p>
        @endforelse

        <form method="POST" action="{{ route('logout') }}" class="mt-8">@csrf<button class="text-sm text-gray-500 underline">Sign out</button></form>
    </div>
</body>
</html>
