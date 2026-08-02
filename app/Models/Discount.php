<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Discount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'organization_id', 'type', 'grant_type', 'value', 'custom_price', 'status', 'starts_at', 'ends_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => DiscountType::class,
            'value' => 'decimal:2',
            'custom_price' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function discountLabel(): string
    {
        if ($this->type === DiscountType::Percentage) {
            return rtrim(rtrim(number_format((float) $this->value, 2), '0'), '.').'%';
        }

        return 'Custom price';
    }

    public function customPriceLabel(): string
    {
        if ($this->custom_price === null) {
            return '—';
        }

        return '£'.number_format((float) $this->custom_price, 2);
    }
}