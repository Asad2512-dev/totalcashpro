<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessAdmin\PurchaseOrderReceiveRequest;
use App\Http\Requests\BusinessAdmin\PurchaseOrderStoreRequest;
use App\Models\PurchaseOrder;
use App\Services\BusinessAdmin\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PurchaseOrderController extends Controller
{
    public function __construct(private readonly PurchaseOrderService $purchaseOrders) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', PurchaseOrder::class);

        return view('business-admin.purchase-orders.index', [
            'orders' => $this->purchaseOrders->list($request->user(), $request->input('status')),
            'meta' => $this->purchaseOrders->formMeta($request->user()),
            'status' => $request->input('status'),
        ]);
    }

    public function show(Request $request, PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('view', $purchaseOrder);
        $order = $this->purchaseOrders->find($request->user(), $purchaseOrder->id);
        $order->load(['goodsReceivedNotes.lines', 'supplier']);

        return view('business-admin.purchase-orders.show', [
            'order' => $order,
            'riders' => \App\Models\Rider::query()
                ->with('user')
                ->where('organization_id', $request->user()->organization_id)
                ->where('is_active', true)
                ->get(),
            'delivery' => \App\Models\Delivery::query()->where('purchase_order_id', $order->id)->first(),
        ]);
    }

    public function store(PurchaseOrderStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', PurchaseOrder::class);

        $po = $this->purchaseOrders->create(
            $request->user(),
            $request->validated(),
            $request->input('lines', []),
        );

        return redirect()
            ->route('business-admin.purchase-orders.show', $po)
            ->with('status', 'Purchase order created.');
    }

    public function update(PurchaseOrderStoreRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('update', $purchaseOrder);

        $this->purchaseOrders->update(
            $request->user(),
            $purchaseOrder,
            $request->validated(),
            $request->input('lines', []),
        );

        return back()->with('status', 'Purchase order updated.');
    }

    public function submit(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('update', $purchaseOrder);
        $this->purchaseOrders->submit($request->user(), $purchaseOrder);

        return back()->with('status', 'Purchase order submitted for approval.');
    }

    public function approve(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('approve', $purchaseOrder);
        $this->purchaseOrders->approve($request->user(), $purchaseOrder);

        return back()->with('status', 'Purchase order approved.');
    }

    public function order(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('approve', $purchaseOrder);
        $this->purchaseOrders->markOrdered($request->user(), $purchaseOrder);

        return back()->with('status', 'Purchase order marked as sent to supplier.');
    }

    public function cancel(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('update', $purchaseOrder);
        $this->purchaseOrders->cancel($request->user(), $purchaseOrder);

        return back()->with('status', 'Purchase order cancelled.');
    }

    public function markSent(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('approve', $purchaseOrder);
        $this->purchaseOrders->markSent($request->user(), $purchaseOrder);

        return back()->with('status', 'Purchase order marked as sent.');
    }

    public function receive(PurchaseOrderReceiveRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('receive', $purchaseOrder);

        $this->purchaseOrders->receiveGoods(
            $request->user(),
            $purchaseOrder,
            $request->input('lines', []),
            $request->input('notes'),
        );

        return back()->with('status', 'Goods received. Stock and finance updated automatically.');
    }

    public function print(Request $request, PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('view', $purchaseOrder);
        $order = $this->purchaseOrders->find($request->user(), $purchaseOrder->id);

        return view('business-admin.purchase-orders.print', [
            'order' => $order->load(['lines', 'supplier', 'branch']),
        ]);
    }
}
