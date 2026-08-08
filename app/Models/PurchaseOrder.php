<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class PurchaseOrder extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'supplier_id', 'po_number', 'status',
        'ordered_at', 'expected_at', 'sent_at', 'sent_by', 'subtotal', 'vat_total', 'total', 'notes',
        'approved_at', 'approved_by', 'supplier_invoice_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => PurchaseOrderStatus::class,
            'ordered_at' => 'date',
            'expected_at' => 'date',
            'sent_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'vat_total' => 'decimal:2',
            'total' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    public function goodsReceivedNotes(): HasMany
    {
        return $this->hasMany(GoodsReceivedNote::class);
    }

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isFullyReceived(): bool
    {
        return $this->lines->every(fn (PurchaseOrderLine $line) => (float) $line->quantity_received >= (float) $line->quantity);
    }

    public function delivery(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function amendments(): HasMany
    {
        return $this->hasMany(PurchaseOrderAmendment::class);
    }

    public function totalAcceptedQuantity(): float
    {
        return (float) $this->goodsReceivedNotes()
            ->with('lines')
            ->get()
            ->sum(fn (GoodsReceivedNote $grn) => $grn->totalAccepted());
    }
}
