<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DeliveryPriority;
use App\Enums\DeliveryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Delivery extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'purchase_order_id', 'rider_id', 'status', 'priority',
        'expected_pickup_at', 'expected_delivery_at',
        'accepted_at', 'at_supplier_at', 'collected_at', 'out_for_delivery_at',
        'arrived_at', 'delivered_at', 'failed_at', 'awaiting_receiving',
        'notes', 'pickup_notes', 'delivery_notes', 'assigned_by',
        'rejection_reason', 'rejected_at',
        'pickup_discrepancy_qty', 'pickup_discrepancy_reason', 'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => DeliveryStatus::class,
            'priority' => DeliveryPriority::class,
            'awaiting_receiving' => 'boolean',
            'expected_pickup_at' => 'datetime',
            'expected_delivery_at' => 'datetime',
            'accepted_at' => 'datetime',
            'at_supplier_at' => 'datetime',
            'collected_at' => 'datetime',
            'out_for_delivery_at' => 'datetime',
            'arrived_at' => 'datetime',
            'delivered_at' => 'datetime',
            'failed_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function events(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DeliveryEvent::class);
    }

    public function proofs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DeliveryProof::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
