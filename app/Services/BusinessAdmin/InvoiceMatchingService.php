<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\BillStatus;
use App\Enums\InvoiceMatchStatus;
use App\Enums\SupplierInvoiceStatus;
use App\Models\Bill;
use App\Models\GoodsReceivedNote;
use App\Models\InvoiceMatch;
use App\Models\ProcurementSetting;
use App\Models\PurchaseOrder;
use App\Models\SupplierDispute;
use App\Models\SupplierInvoice;
use App\Models\SupplierInvoiceLine;
use App\Models\User;
use App\Support\Finance\VatCalculator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class InvoiceMatchingService implements ServiceInterface
{
    /**
     * @param  list<array{description: string, quantity: float, unit_cost: float, vat_rate?: float, purchase_order_line_id?: int, inventory_item_id?: int}>  $lines
     */
    public function createInvoice(
        User $user,
        PurchaseOrder $po,
        array $data,
        array $lines,
        ?int $grnId = null,
    ): SupplierInvoice {
        if ((int) $po->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $duplicate = SupplierInvoice::query()
            ->where('organization_id', $user->organization_id)
            ->where('supplier_id', $po->supplier_id)
            ->where('invoice_no', $data['invoice_no'])
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'invoice_no' => 'This invoice number already exists for this supplier.',
            ]);
        }

        return DB::transaction(function () use ($user, $po, $data, $lines, $grnId): SupplierInvoice {
            $subtotal = 0.0;
            $vat = 0.0;

            $invoice = SupplierInvoice::query()->create([
                'organization_id' => $po->organization_id,
                'branch_id' => $po->branch_id,
                'supplier_id' => $po->supplier_id,
                'purchase_order_id' => $po->id,
                'goods_received_note_id' => $grnId,
                'invoice_no' => $data['invoice_no'],
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'],
                'currency' => $data['currency'] ?? 'GBP',
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => SupplierInvoiceStatus::Pending->value,
                'amount' => 0,
                'net_amount' => 0,
                'vat_amount' => 0,
                'gross_amount' => 0,
                'amount_paid' => 0,
            ]);

            foreach ($lines as $line) {
                $qty = (float) ($line['quantity'] ?? 0);
                $unitCost = (float) ($line['unit_cost'] ?? 0);
                $vatRate = (float) ($line['vat_rate'] ?? 20);
                $net = round($qty * $unitCost, 2);
                $amounts = VatCalculator::fromNet($net, $vatRate);
                $subtotal += $amounts['net'];
                $vat += $amounts['vat'];

                SupplierInvoiceLine::query()->create([
                    'supplier_invoice_id' => $invoice->id,
                    'purchase_order_line_id' => $line['purchase_order_line_id'] ?? null,
                    'inventory_item_id' => $line['inventory_item_id'] ?? null,
                    'description' => $line['description'],
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'vat_rate' => $vatRate,
                    'line_total' => $amounts['gross'],
                ]);
            }

            $gross = round($subtotal + $vat, 2);
            $invoice->update([
                'net_amount' => round($subtotal, 2),
                'vat_amount' => round($vat, 2),
                'gross_amount' => $gross,
                'amount' => $gross,
            ]);

            $match = $this->performMatch($user, $po, $invoice, $grnId);

            if ($match->status === InvoiceMatchStatus::Matched && ProcurementSetting::forOrganization((int) $user->organization_id)->auto_create_bill_on_match) {
                $this->createBillFromInvoice($user, $po, $invoice);
            }

            return $invoice->fresh(['lines', 'invoiceMatch']);
        });
    }

    public function performMatch(
        User $user,
        PurchaseOrder $po,
        SupplierInvoice $invoice,
        ?int $grnId = null,
    ): InvoiceMatch {
        $po->loadMissing('lines');
        $invoice->loadMissing('lines');

        $poQty = (float) $po->lines->sum('quantity');
        $poAmount = (float) $po->subtotal;

        $grnQty = 0.0;
        $grnAmount = 0.0;

        if ($grnId) {
            $grn = GoodsReceivedNote::query()->with('lines.purchaseOrderLine')->findOrFail($grnId);
            $grnQty = $grn->totalAccepted();
            foreach ($grn->lines as $line) {
                $poLine = $line->purchaseOrderLine;
                if ($poLine) {
                    $grnAmount += round((float) $line->quantity_accepted * (float) $poLine->unit_cost, 2);
                }
            }
        } else {
            $grnQty = $po->totalAcceptedQuantity();
            $grnAmount = $this->acceptedAmountForPo($po);
        }

        $invoiceQty = (float) $invoice->lines->sum('quantity');
        $invoiceAmount = (float) $invoice->net_amount;

        $qtyVariance = round($invoiceQty - $grnQty, 3);
        $priceVariance = round($invoiceAmount - ($grnQty > 0 ? $grnAmount : $poAmount), 2);

        $settings = ProcurementSetting::forOrganization((int) $user->organization_id);
        $qtyTolerance = $grnQty > 0 ? $grnQty * ((float) $settings->quantity_tolerance_percent / 100) : 0;
        $priceTolerance = ($grnAmount > 0 ? $grnAmount : $poAmount) * ((float) $settings->price_tolerance_percent / 100);

        $qtyOk = abs($qtyVariance) <= max($qtyTolerance, 0.001);
        $priceOk = abs($priceVariance) <= max($priceTolerance, 0.01);

        $status = ($qtyOk && $priceOk)
            ? InvoiceMatchStatus::Matched
            : InvoiceMatchStatus::Mismatch;

        if ($invoiceQty > $grnQty && $grnQty > 0) {
            $status = InvoiceMatchStatus::Mismatch;
        }

        $match = InvoiceMatch::query()->updateOrCreate(
            ['supplier_invoice_id' => $invoice->id],
            [
                'organization_id' => $po->organization_id,
                'branch_id' => $po->branch_id,
                'supplier_id' => $po->supplier_id,
                'purchase_order_id' => $po->id,
                'goods_received_note_id' => $grnId,
                'status' => $status->value,
                'po_quantity' => $poQty,
                'grn_quantity' => $grnQty,
                'invoice_quantity' => $invoiceQty,
                'po_amount' => $poAmount,
                'grn_amount' => $grnAmount,
                'invoice_amount' => $invoiceAmount,
                'quantity_variance' => $qtyVariance,
                'price_variance' => $priceVariance,
            ],
        );

        if ($status === InvoiceMatchStatus::Mismatch) {
            SupplierDispute::query()->firstOrCreate(
                [
                    'supplier_invoice_id' => $invoice->id,
                    'status' => 'open',
                ],
                [
                    'organization_id' => $po->organization_id,
                    'supplier_id' => $po->supplier_id,
                    'invoice_match_id' => $match->id,
                    'disputed_amount' => abs($priceVariance),
                    'reason' => sprintf('Qty variance: %s, Price variance: £%s', $qtyVariance, number_format(abs($priceVariance), 2)),
                    'created_by' => $user->id,
                ],
            );
            $invoice->update(['status' => SupplierInvoiceStatus::Disputed->value]);
        } else {
            $invoice->update(['status' => SupplierInvoiceStatus::Outstanding->value]);
        }

        return $match;
    }

    public function approveException(User $user, InvoiceMatch $match, ?string $notes = null): InvoiceMatch
    {
        if ((int) $match->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        $match->update([
            'status' => InvoiceMatchStatus::ApprovedException->value,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'notes' => $notes,
        ]);

        $invoice = $match->supplierInvoice;
        $invoice?->update(['status' => SupplierInvoiceStatus::Outstanding->value]);

        if ($invoice && ProcurementSetting::forOrganization((int) $user->organization_id)->auto_create_bill_on_match) {
            $this->createBillFromInvoice($user, $match->purchaseOrder, $invoice);
        }

        SupplierDispute::query()
            ->where('invoice_match_id', $match->id)
            ->update(['status' => 'resolved', 'resolved_by' => $user->id, 'resolved_at' => now()]);

        return $match->refresh();
    }

    private function acceptedAmountForPo(PurchaseOrder $po): float
    {
        $amount = 0.0;
        foreach ($po->goodsReceivedNotes()->with('lines.purchaseOrderLine')->get() as $grn) {
            foreach ($grn->lines as $line) {
                $poLine = $line->purchaseOrderLine;
                if ($poLine) {
                    $amount += round((float) $line->quantity_accepted * (float) $poLine->unit_cost, 2);
                }
            }
        }

        return $amount;
    }

    private function createBillFromInvoice(User $user, PurchaseOrder $po, SupplierInvoice $invoice): void
    {
        if (Bill::query()->where('supplier_invoice_id', $invoice->id)->exists()) {
            return;
        }

        Bill::query()->create([
            'organization_id' => $po->organization_id,
            'branch_id' => $po->branch_id,
            'supplier_invoice_id' => $invoice->id,
            'purchase_order_id' => $po->id,
            'title' => 'Supplier bill · '.$invoice->invoice_no,
            'vendor' => $po->supplier?->name,
            'category' => 'suppliers',
            'amount' => $invoice->gross_amount,
            'net_amount' => $invoice->net_amount,
            'vat_amount' => $invoice->vat_amount,
            'gross_amount' => $invoice->gross_amount,
            'due_date' => $invoice->due_date,
            'status' => BillStatus::Pending->value,
            'approved_at' => now(),
            'created_by' => $user->id,
        ]);

        $po->update(['supplier_invoice_id' => $invoice->id]);
    }
}
