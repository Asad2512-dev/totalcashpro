<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupplierInvoice;
use App\Services\BusinessAdmin\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierService $supplier,
    ) {}

    public function index(Request $request): View
    {
        $data = $this->supplier->list($request->user());

        return view('business-admin.suppliers.index', array_merge($data, [
            'tab' => $request->input('tab', 'suppliers'),
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->supplier->storeSupplier($request->user(), $validated);

        return redirect()
            ->route('business-admin.suppliers')
            ->with('status', 'Supplier created.');
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

        $this->supplier->storeInvoice($request->user(), $validated);

        return redirect()
            ->route('business-admin.suppliers', ['tab' => 'invoices'])
            ->with('status', 'Invoice created.');
    }

    public function markPaid(Request $request, SupplierInvoice $invoice): RedirectResponse
    {
        $this->supplier->markInvoicePaid($request->user(), (int) $invoice->id);

        return redirect()
            ->route('business-admin.suppliers', ['tab' => 'invoices'])
            ->with('status', 'Invoice marked as paid.');
    }
}
