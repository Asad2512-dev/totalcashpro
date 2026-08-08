<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\PurchaseOrder;
use App\Models\Rider;
use App\Services\BusinessAdmin\RiderDeliveryService;
use App\Services\BusinessAdmin\RiderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class RiderController extends Controller
{
    public function __construct(
        private readonly RiderService $riders,
        private readonly RiderDeliveryService $deliveries,
    ) {}

    public function index(Request $request): View
    {
        return view('business-admin.riders.index', [
            'riders' => $this->riders->list($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'vehicle' => ['nullable', 'string', 'max:100'],
            'branch_id' => ['nullable', 'integer'],
        ]);

        $this->riders->create($request->user(), $validated);

        return back()->with('status', 'Rider created.');
    }

    public function toggle(Request $request, Rider $rider): RedirectResponse
    {
        $this->riders->toggleActive($request->user(), $rider, (bool) $request->boolean('active', true));

        return back()->with('status', 'Rider updated.');
    }

    public function assign(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $validated = $request->validate([
            'rider_id' => ['required', 'integer'],
            'expected_pickup_at' => ['nullable', 'date'],
            'expected_delivery_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $rider = Rider::query()
            ->where('organization_id', $request->user()->organization_id)
            ->findOrFail((int) $validated['rider_id']);

        $this->deliveries->assign(
            $request->user(),
            $purchaseOrder,
            $rider,
            $validated['expected_pickup_at'] ?? null,
            $validated['expected_delivery_at'] ?? null,
            $validated['notes'] ?? null,
        );

        return back()->with('status', 'Rider assigned.');
    }
}
