<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FinanceStatus;
use App\Models\Concerns\HasFinanceAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class FinanceSupplierPayment extends Model
{
    use HasFinanceAttachments;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'supplier_invoice_id',
        'bank_account_id',
        'net_amount',
        'vat_amount',
        'gross_amount',
        'payment_date',
        'reference',
        'payment_method',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'net_amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'payment_date' => 'date',
            'status' => FinanceStatus::class,
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

    public function supplierInvoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceBankAccount::class, 'bank_account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
