<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AlertPriority;
use App\Enums\AlertStatus;
use App\Enums\AlertType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BusinessAlert extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'alert_type', 'priority', 'status',
        'title', 'message', 'reference_type', 'reference_id', 'action_url', 'metadata',
        'acknowledged_at', 'acknowledged_by', 'resolved_at', 'resolved_by',
    ];

    protected function casts(): array
    {
        return [
            'alert_type' => AlertType::class,
            'priority' => AlertPriority::class,
            'status' => AlertStatus::class,
            'metadata' => 'array',
            'acknowledged_at' => 'datetime',
            'resolved_at' => 'datetime',
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
