<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PurchaseOrderLine extends Model
{
    protected $fillable = [
        'purchase_order_id', 'inventory_item_id', 'description', 'quantity',
        'quantity_received', 'unit_cost', 'vat_rate', 'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'quantity_received' => 'decimal:3',
            'unit_cost' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function quantityOutstanding(): float
    {
        return max(0, (float) $this->quantity - (float) $this->quantity_received);
    }
}
