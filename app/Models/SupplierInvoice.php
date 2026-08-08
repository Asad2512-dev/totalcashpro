<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SupplierInvoiceStatus;
use App\Models\Concerns\HasFinanceAttachments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class SupplierInvoice extends Model
{
    use HasFactory;
    use HasFinanceAttachments;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'supplier_id',
        'purchase_order_id',
        'goods_received_note_id',
        'invoice_no',
        'invoice_date',
        'due_date',
        'amount',
        'net_amount',
        'vat_amount',
        'gross_amount',
        'amount_paid',
        'description',
        'status',
        'approved_at',
        'paid_date',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'paid_date' => 'date',
            'amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'status' => SupplierInvoiceStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function outstandingAmount(): float
    {
        return max(0, (float) $this->gross_amount - (float) $this->amount_paid);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FinanceSupplierPayment::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SupplierInvoiceLine::class);
    }

    public function goodsReceivedNote(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivedNote::class, 'goods_received_note_id');
    }

    public function invoiceMatch(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InvoiceMatch::class);
    }
}
