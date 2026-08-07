<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class FinanceBankAccount extends Model
{
    protected $fillable = [
        'organization_id',
        'branch_id',
        'name',
        'bank_name',
        'sort_code',
        'account_number_last4',
        'currency',
        'opening_balance',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
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
