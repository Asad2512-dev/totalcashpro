<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceMatchStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class InvoiceMatch extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'supplier_id',
        'purchase_order_id', 'goods_received_note_id', 'supplier_invoice_id',
        'status', 'po_quantity', 'grn_quantity', 'invoice_quantity',
        'po_amount', 'grn_amount', 'invoice_amount',
        'quantity_variance', 'price_variance', 'notes',
        'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceMatchStatus::class,
            'po_quantity' => 'decimal:3',
            'grn_quantity' => 'decimal:3',
            'invoice_quantity' => 'decimal:3',
            'po_amount' => 'decimal:2',
            'grn_amount' => 'decimal:2',
            'invoice_amount' => 'decimal:2',
            'quantity_variance' => 'decimal:3',
            'price_variance' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function goodsReceivedNote(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivedNote::class);
    }

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class);
    }
}
