<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class GoodsReceivedNote extends Model
{
    protected $fillable = [
        'purchase_order_id', 'organization_id', 'branch_id', 'received_at', 'received_by', 'notes',
    ];

    protected function casts(): array
    {
        return ['received_at' => 'date'];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(GoodsReceivedLine::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
