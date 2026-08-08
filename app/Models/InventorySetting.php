<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class InventorySetting extends Model
{
    protected $fillable = [
        'organization_id', 'stocktake_weekday', 'stocktake_time', 'stocktake_reminders',
    ];

    protected function casts(): array
    {
        return [
            'stocktake_reminders' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
