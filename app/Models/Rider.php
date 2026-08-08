<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Rider extends Model
{
    protected $fillable = [
        'organization_id', 'user_id', 'branch_ids', 'phone', 'vehicle', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'branch_ids' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function servesBranch(int $branchId): bool
    {
        $ids = $this->branch_ids ?? [];

        return empty($ids) || in_array($branchId, $ids, true);
    }
}
