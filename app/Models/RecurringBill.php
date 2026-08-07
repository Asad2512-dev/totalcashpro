<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RecurringBillFrequency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RecurringBill extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'title', 'vendor', 'category', 'amount',
        'frequency', 'next_due_date', 'last_generated_date', 'finance_bank_account_id', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'frequency' => RecurringBillFrequency::class,
            'amount' => 'decimal:2',
            'next_due_date' => 'date',
            'last_generated_date' => 'date',
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
}
