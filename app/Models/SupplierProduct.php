<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SupplierProduct extends Model
{
    protected $fillable = [
        'organization_id', 'supplier_id', 'inventory_item_id',
        'supplier_sku', 'pack_size', 'unit', 'unit_cost', 'vat_rate', 'moq',
        'order_multiple', 'lead_time_days', 'is_primary', 'active',
        'effective_from', 'effective_until',
    ];

    protected function casts(): array
    {
        return [
            'pack_size' => 'integer',
            'unit_cost' => 'decimal:2',
            'moq' => 'decimal:2',
            'lead_time_days' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
