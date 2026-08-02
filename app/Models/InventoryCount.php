<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class InventoryCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'item_id',
        'diff_pcs',
        'new_pcs',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'diff_pcs' => 'integer',
            'new_pcs' => 'integer',
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

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
