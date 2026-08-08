<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class GoodsReceivedLine extends Model
{
    protected $fillable = [
        'goods_received_note_id', 'purchase_order_line_id',
        'quantity_received', 'quantity_damaged', 'quantity_missing', 'quantity_accepted',
    ];

    protected function casts(): array
    {
        return [
            'quantity_received' => 'decimal:3',
            'quantity_damaged' => 'decimal:3',
            'quantity_missing' => 'decimal:3',
            'quantity_accepted' => 'decimal:3',
        ];
    }

    public function note(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'goods_received_note_id');
    }

    public function purchaseOrderLine(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderLine::class);
    }
}
