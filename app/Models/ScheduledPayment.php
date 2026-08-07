<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ScheduledPaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class ScheduledPayment extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'payable_type', 'payable_id',
        'amount', 'scheduled_date', 'status', 'finance_bank_account_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'scheduled_date' => 'date',
            'status' => ScheduledPaymentStatus::class,
        ];
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceBankAccount::class, 'finance_bank_account_id');
    }
}
