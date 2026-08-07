<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PettyCashAccount extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'name', 'balance', 'float_amount',
        'opening_balance', 'custodian_user_id', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'float_amount' => 'decimal:2',
            'opening_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
