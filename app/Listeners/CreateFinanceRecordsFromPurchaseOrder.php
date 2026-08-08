<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\BillStatus;
use App\Enums\NotificationCategory;
use App\Enums\SupplierInvoiceStatus;
use App\Events\PurchaseOrderReceived;
use App\Models\AppNotification;
use App\Models\Bill;
use App\Models\SupplierInvoice;
use App\Support\Finance\VatCalculator;
use Illuminate\Contracts\Queue\ShouldQueue;

final class CreateFinanceRecordsFromPurchaseOrder implements ShouldQueue
{
    public function handle(PurchaseOrderReceived $event): void
    {
        $po = $event->purchaseOrder->loadMissing(['supplier']);
        $grn = $event->goodsReceivedNote->loadMissing(['lines.purchaseOrderLine']);

        $net = 0.0;
        $vat = 0.0;

        foreach ($grn->lines as $line) {
            $poLine = $line->purchaseOrderLine;
            if ($poLine === null) {
                continue;
            }

            $qty = max(0, (float) $line->quantity_accepted);
            $lineNet = round($qty * (float) $poLine->unit_cost, 2);
            $amounts = VatCalculator::fromNet($lineNet, (float) ($poLine->vat_rate ?? 20));
            $net += $amounts['net'];
            $vat += $amounts['vat'];
        }

        $gross = round($net + $vat, 2);

        if ($gross <= 0) {
            return;
        }

        $invoice = $po->supplier_invoice_id
            ? SupplierInvoice::query()->find($po->supplier_invoice_id)
            : null;

        if ($invoice === null) {
            $invoice = SupplierInvoice::query()->create([
                'organization_id' => $po->organization_id,
                'branch_id' => $po->branch_id,
                'supplier_id' => $po->supplier_id,
                'purchase_order_id' => $po->id,
                'goods_received_note_id' => $grn->id,
                'invoice_no' => 'INV-'.$po->po_number,
                'invoice_date' => $grn->received_at,
                'due_date' => $grn->received_at->copy()->addDays(30),
                'amount' => $gross,
                'net_amount' => $net,
                'vat_amount' => $vat,
                'gross_amount' => $gross,
                'amount_paid' => 0,
                'description' => 'Auto-generated from PO '.$po->po_number,
                'status' => SupplierInvoiceStatus::Outstanding->value,
                'approved_at' => now(),
            ]);

            Bill::query()->create([
                'organization_id' => $po->organization_id,
                'branch_id' => $po->branch_id,
                'supplier_invoice_id' => $invoice->id,
                'purchase_order_id' => $po->id,
                'title' => 'Supplier bill · '.$po->po_number,
                'vendor' => $po->supplier?->name,
                'category' => 'suppliers',
                'amount' => $gross,
                'net_amount' => $net,
                'vat_amount' => $vat,
                'gross_amount' => $gross,
                'due_date' => $invoice->due_date,
                'status' => BillStatus::Pending->value,
                'approved_at' => now(),
                'created_by' => $event->actor->id,
            ]);

            $po->update(['supplier_invoice_id' => $invoice->id]);
        } else {
            $invoice->update([
                'net_amount' => (float) $invoice->net_amount + $net,
                'vat_amount' => (float) $invoice->vat_amount + $vat,
                'gross_amount' => (float) $invoice->gross_amount + $gross,
                'amount' => (float) $invoice->gross_amount + $gross,
            ]);

            Bill::query()
                ->where('supplier_invoice_id', $invoice->id)
                ->update([
                    'net_amount' => (float) $invoice->net_amount,
                    'vat_amount' => (float) $invoice->vat_amount,
                    'gross_amount' => (float) $invoice->gross_amount,
                    'amount' => (float) $invoice->gross_amount,
                ]);
        }

        $admins = \App\Models\User::query()
            ->where('organization_id', $po->organization_id)
            ->whereHas('role', fn ($q) => $q->where('slug', 'admin'))
            ->get();

        foreach ($admins as $admin) {
            AppNotification::query()->create([
                'user_id' => $admin->id,
                'title' => 'Goods received · '.$po->po_number,
                'body' => 'Stock updated. Finance records synced automatically.',
                'type' => 'purchase_received',
                'category' => NotificationCategory::Finance->value,
                'data' => ['purchase_order_id' => $po->id],
            ]);
        }
    }
}
