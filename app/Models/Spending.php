<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FinanceStatus;
use App\Models\Concerns\HasFinanceAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Spending extends Model
{
    use HasFinanceAttachments;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'title',
        'category',
        'amount',
        'net_amount',
        'vat_amount',
        'gross_amount',
        'status',
        'bank_account_id',
        'spent_date',
        'payment_method',
        'approved_at',
        'paid_at',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'spent_date' => 'date',
            'amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'status' => FinanceStatus::class,
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
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

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceBankAccount::class, 'bank_account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
