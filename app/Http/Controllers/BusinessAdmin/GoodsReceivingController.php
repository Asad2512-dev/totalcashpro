<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceivedNote;
use App\Models\PurchaseOrder;
use App\Services\BusinessAdmin\GoodsReceivingService;
use App\Services\BusinessAdmin\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class GoodsReceivingController extends Controller
{
    public function __construct(
        private readonly GoodsReceivingService $receiving,
        private readonly PurchaseOrderService $purchaseOrders,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $branchId = session('business_admin.branch_id');

        $awaiting = PurchaseOrder::query()
            ->with(['supplier', 'delivery'])
            ->where('organization_id', $user->organization_id)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['ordered', 'partial'])
            ->orderByDesc('updated_at')
            ->get();

        return view('business-admin.receiving.index', [
            'orders' => $awaiting,
        ]);
    }

    public function create(Request $request, PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('receive', $purchaseOrder);
        $order = $this->purchaseOrders->find($request->user(), $purchaseOrder->id);

        return view('business-admin.receiving.create', [
            'order' => $order->load(['lines', 'supplier', 'delivery', 'goodsReceivedNotes']),
        ]);
    }

    public function store(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('receive', $purchaseOrder);

        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
            'allow_over_delivery' => ['sometimes', 'boolean'],
            'lines' => ['required', 'array'],
            'lines.*.purchase_order_line_id' => ['required', 'integer'],
            'lines.*.quantity_received' => ['required', 'numeric', 'min:0'],
            'lines.*.quantity_damaged' => ['nullable', 'numeric', 'min:0'],
            'lines.*.quantity_missing' => ['nullable', 'numeric', 'min:0'],
            'lines.*.accept_over_delivery' => ['sometimes', 'boolean'],
        ]);

        $grn = $this->purchaseOrders->receiveGoods(
            $request->user(),
            $purchaseOrder,
            $validated['lines'],
            $validated['notes'] ?? null,
            (bool) ($validated['allow_over_delivery'] ?? false),
        );

        return redirect()
            ->route('business-admin.receiving.show', $grn)
            ->with('status', 'Goods received. Inventory updated by accepted quantities only.');
    }

    public function show(Request $request, GoodsReceivedNote $goodsReceivedNote): View
    {
        abort_unless((int) $goodsReceivedNote->organization_id === (int) $request->user()->organization_id, 403);

        return view('business-admin.receiving.show', [
            'grn' => $goodsReceivedNote->load(['lines.purchaseOrderLine', 'purchaseOrder.supplier', 'receiver', 'delivery']),
        ]);
    }

    public function print(Request $request, GoodsReceivedNote $goodsReceivedNote): View
    {
        abort_unless((int) $goodsReceivedNote->organization_id === (int) $request->user()->organization_id, 403);

        return view('business-admin.receiving.print', [
            'grn' => $goodsReceivedNote->load(['lines.purchaseOrderLine', 'purchaseOrder.supplier', 'purchaseOrder.branch', 'receiver']),
        ]);
    }
}
