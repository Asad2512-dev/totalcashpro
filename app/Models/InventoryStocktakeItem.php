<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class InventoryStocktakeItem extends Model
{
    protected $fillable = [
        'inventory_stocktake_id', 'inventory_item_id',
        'system_qty', 'counted_qty', 'difference_qty',
        'par_level', 'on_order_qty', 'suggested_order_qty', 'ordered_qty',
        'supplier_id', 'excluded_from_order', 'override_reason', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'system_qty' => 'decimal:3',
            'counted_qty' => 'decimal:3',
            'difference_qty' => 'decimal:3',
            'par_level' => 'decimal:3',
            'on_order_qty' => 'decimal:3',
            'suggested_order_qty' => 'decimal:3',
            'ordered_qty' => 'decimal:3',
            'excluded_from_order' => 'boolean',
        ];
    }

    public function stocktake(): BelongsTo
    {
        return $this->belongsTo(InventoryStocktake::class, 'inventory_stocktake_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function effectiveOrderQty(): float
    {
        if ($this->excluded_from_order) {
            return 0.0;
        }

        return (float) ($this->ordered_qty ?? $this->suggested_order_qty);
    }
}
