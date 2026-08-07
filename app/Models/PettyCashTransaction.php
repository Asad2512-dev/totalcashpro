<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PettyCashTransaction extends Model
{
    protected $fillable = [
        'petty_cash_account_id', 'organization_id', 'branch_id',
        'type', 'amount', 'description', 'transaction_date', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(PettyCashAccount::class, 'petty_cash_account_id');
    }
}
