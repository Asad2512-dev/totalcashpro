<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class InventoryItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'category_id',
        'name',
        'sku',
        'barcode',
        'brand',
        'cost_price',
        'selling_price',
        'supplier_id',
        'batch_number',
        'expiry_date',
        'packaging',
        'pcs_per_box',
        'stock_total_pcs',
        'stock_limit',
        'unit',
        'par_level',
        'min_level',
        'max_level',
        'order_multiple',
        'pack_size',
        'lead_time_days',
    ];

    protected function casts(): array
    {
        return [
            'pcs_per_box' => 'integer',
            'stock_total_pcs' => 'integer',
            'stock_limit' => 'integer',
            'par_level' => 'integer',
            'min_level' => 'integer',
            'max_level' => 'integer',
            'order_multiple' => 'integer',
            'pack_size' => 'integer',
            'lead_time_days' => 'integer',
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'expiry_date' => 'date',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function counts(): HasMany
    {
        return $this->hasMany(InventoryCount::class, 'item_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function isLowStock(): bool
    {
        $par = $this->par_level > 0 ? $this->par_level : $this->stock_limit;

        return $par > 0 && $this->stock_total_pcs <= $par;
    }

    public function parLevel(): int
    {
        return $this->par_level > 0 ? (int) $this->par_level : (int) $this->stock_limit;
    }
}
