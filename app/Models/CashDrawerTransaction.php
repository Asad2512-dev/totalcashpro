<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CashDrawerTransaction extends Model
{
    protected $fillable = [
        'cash_drawer_id', 'organization_id', 'branch_id',
        'type', 'amount', 'description', 'cash_up_id', 'created_by',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function cashDrawer(): BelongsTo
    {
        return $this->belongsTo(CashDrawer::class);
    }
}
