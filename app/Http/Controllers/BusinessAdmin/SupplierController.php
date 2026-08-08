<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceivedNote;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use App\Services\BusinessAdmin\InvoiceMatchingService;
use App\Services\BusinessAdmin\ProcurementDashboardService;
use App\Services\BusinessAdmin\SupplierPerformanceService;
use App\Services\BusinessAdmin\SupplierProductService;
use App\Services\BusinessAdmin\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierService $suppliers,
        private readonly SupplierProductService $products,
        private readonly SupplierPerformanceService $performance,
        private readonly InvoiceMatchingService $matching,
        private readonly ProcurementDashboardService $procurement,
    ) {}

    public function dashboard(Request $request): View
    {
        return view('business-admin.procurement.dashboard', [
            'summary' => $this->procurement->summary($request->user()),
        ]);
    }

    public function index(Request $request): View
    {
        $data = $this->suppliers->list(
            $request->user(),
            $request->input('search'),
            $request->input('status'),
        );

        return view('business-admin.suppliers.index', array_merge($data, [
            'tab' => $request->input('tab', 'suppliers'),
            'search' => $request->input('search'),
            'status' => $request->input('status'),
        ]));
    }

    public function show(Request $request, Supplier $supplier): View
    {
        $this->authorize('view', $supplier);

        return view('business-admin.suppliers.show', [
            'supplier' => $this->suppliers->find($request->user(), $supplier),
            'performance' => $this->performance->metricsForSupplier($request->user(), $supplier),
            'tab' => $request->input('tab', 'overview'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Supplier::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'trading_name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'payment_terms' => ['nullable', 'string', 'max:100'],
            'currency' => ['nullable', 'string', 'max:3'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $supplier = $this->suppliers->storeSupplier($request->user(), $validated);

        return redirect()
            ->route('business-admin.suppliers.show', $supplier)
            ->with('status', 'Supplier created.');
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('update', $supplier);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'trading_name' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'string', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'payment_terms' => ['nullable', 'string', 'max:100'],
            'currency' => ['nullable', 'string', 'max:3'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'min_order_value' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
        ]);

        $this->suppliers->updateSupplier($request->user(), $supplier, $validated);

        return back()->with('status', 'Supplier updated.');
    }

    public function archive(Request $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('update', $supplier);
        $this->suppliers->archive($request->user(), $supplier);

        return redirect()->route('business-admin.suppliers')->with('status', 'Supplier archived.');
    }

    public function storeContact(Request $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('update', $supplier);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        $this->suppliers->storeContact($request->user(), $supplier, $validated);

        return back()->with('status', 'Contact added.');
    }

    public function storeProduct(Request $request, Supplier $supplier): RedirectResponse
    {
        $this->authorize('update', $supplier);

        $validated = $request->validate([
            'inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'supplier_sku' => ['nullable', 'string', 'max:100'],
            'pack_size' => ['nullable', 'integer', 'min:1'],
            'unit' => ['nullable', 'string', 'max:20'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'vat_rate' => ['nullable', 'numeric', 'min:0'],
            'moq' => ['nullable', 'numeric', 'min:0'],
            'order_multiple' => ['nullable', 'integer', 'min:1'],
            'lead_time_days' => ['nullable', 'integer', 'min:0'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        $this->products->store($request->user(), $supplier, $validated);

        return back()->with('status', 'Product mapped to supplier.');
    }

    public function storeInvoice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'invoice_no' => ['required', 'string', 'max:100'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $this->suppliers->storeInvoice($request->user(), $validated);

        return redirect()
            ->route('business-admin.suppliers', ['tab' => 'invoices'])
            ->with('status', 'Invoice created.');
    }

    public function storeMatchedInvoice(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('view', $purchaseOrder);

        $validated = $request->validate([
            'invoice_no' => ['required', 'string', 'max:100'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'goods_received_note_id' => ['nullable', 'integer'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.description' => ['required', 'string'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0'],
            'lines.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'lines.*.purchase_order_line_id' => ['nullable', 'integer'],
        ]);

        $invoice = $this->matching->createInvoice(
            $request->user(),
            $purchaseOrder,
            $validated,
            $validated['lines'],
            isset($validated['goods_received_note_id']) ? (int) $validated['goods_received_note_id'] : null,
        );

        return redirect()
            ->route('business-admin.purchase-orders.show', $purchaseOrder)
            ->with('status', 'Invoice recorded. Match status: '.$invoice->invoiceMatch?->status?->label());
    }

    public function approveMatch(Request $request, \App\Models\InvoiceMatch $invoiceMatch): RedirectResponse
    {
        abort_unless((int) $invoiceMatch->organization_id === (int) $request->user()->organization_id, 403);

        $validated = $request->validate(['notes' => ['nullable', 'string', 'max:500']]);
        $this->matching->approveException($request->user(), $invoiceMatch, $validated['notes'] ?? null);

        return back()->with('status', 'Match exception approved.');
    }

    public function markPaid(Request $request, SupplierInvoice $invoice): RedirectResponse
    {
        $this->suppliers->markInvoicePaid($request->user(), (int) $invoice->id);

        return redirect()
            ->route('business-admin.suppliers', ['tab' => 'invoices'])
            ->with('status', 'Invoice marked as paid.');
    }
}
