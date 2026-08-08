<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SupplierDispute extends Model
{
    protected $fillable = [
        'organization_id', 'supplier_id', 'supplier_invoice_id', 'invoice_match_id',
        'disputed_amount', 'status', 'reason', 'resolution_notes',
        'created_by', 'resolved_by', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'disputed_amount' => 'decimal:2',
            'resolved_at' => 'datetime',
        ];
    }

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function invoiceMatch(): BelongsTo
    {
        return $this->belongsTo(InvoiceMatch::class);
    }
}
