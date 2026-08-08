<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BudgetCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BudgetLine extends Model
{
    protected $fillable = ['budget_id', 'category', 'amount'];

    protected function casts(): array
    {
        return [
            'category' => BudgetCategory::class,
            'amount' => 'decimal:2',
        ];
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }
}
