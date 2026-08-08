<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InventoryStocktakeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class InventoryStocktake extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'week_start', 'week_end', 'status',
        'created_by', 'reviewed_by', 'approved_by',
        'submitted_at', 'approved_at', 'notes',
        'client_reference', 'idempotency_key', 'device_id', 'source',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
            'status' => InventoryStocktakeStatus::class,
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryStocktakeItem::class);
    }

    public function isEditableByStaff(): bool
    {
        $status = $this->status instanceof InventoryStocktakeStatus
            ? $this->status
            : InventoryStocktakeStatus::tryFrom((string) $this->status);

        return $status?->isEditableByStaff() ?? false;
    }
}
