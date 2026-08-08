<?php

declare(strict_types=1);

namespace App\Http\Controllers\Rider;

use App\Enums\DeliveryStatus;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Services\BusinessAdmin\RiderDeliveryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class DashboardController extends Controller
{
    public function __construct(private readonly RiderDeliveryService $deliveries) {}

    public function __invoke(Request $request): View
    {
        $rider = $request->attributes->get('rider');

        return view('rider.dashboard', [
            'deliveries' => $this->deliveries->todayForRider($rider),
            'stats' => $this->deliveries->statsForRider($rider),
            'rider' => $rider,
        ]);
    }

    public function advance(Request $request, Delivery $delivery): RedirectResponse
    {
        $rider = $request->attributes->get('rider');
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $status = DeliveryStatus::from($validated['status']);
        $this->deliveries->advanceStatus($rider, $delivery, $status, $validated['notes'] ?? null);

        return back()->with('status', 'Delivery updated.');
    }

    public function accept(Request $request, Delivery $delivery): RedirectResponse
    {
        $rider = $request->attributes->get('rider');
        $this->deliveries->accept($rider, $delivery);

        return back()->with('status', 'Delivery accepted.');
    }

    public function reject(Request $request, Delivery $delivery): RedirectResponse
    {
        $rider = $request->attributes->get('rider');
        $validated = $request->validate(['reason' => ['required', 'string', 'max:500']]);
        $this->deliveries->reject($rider, $delivery, $validated['reason']);

        return back()->with('status', 'Delivery rejected.');
    }
}
