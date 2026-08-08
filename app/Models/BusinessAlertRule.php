<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BusinessAlertRule extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'rule_type', 'threshold_value',
        'threshold_percent', 'threshold_days', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'threshold_value' => 'decimal:2',
            'threshold_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
