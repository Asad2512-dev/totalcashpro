<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SupplierInvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SupplierInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'supplier_id',
        'invoice_no',
        'invoice_date',
        'due_date',
        'amount',
        'description',
        'status',
        'paid_date',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'paid_date' => 'date',
            'amount' => 'decimal:2',
            'status' => SupplierInvoiceStatus::class,
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
}
