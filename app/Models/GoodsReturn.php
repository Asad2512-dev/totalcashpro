<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class GoodsReturn extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'supplier_id',
        'purchase_order_id', 'goods_received_note_id', 'inventory_item_id',
        'quantity', 'reason', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:3'];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
