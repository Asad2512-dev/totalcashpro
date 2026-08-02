<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Plan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'badge', 'description', 'price_monthly', 'currency',
        'billing_interval', 'features', 'is_featured', 'is_active', 'is_public', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'features' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function formattedPrice(): string
    {
        if ((float) $this->price_monthly <= 0 && $this->slug === 'enterprise') {
            return 'Custom';
        }

        return '£'.number_format((float) $this->price_monthly, 2);
    }

    /**
     * Marketing bullet lines for UI cards (supports legacy list + structured features).
     *
     * @return list<string>
     */
    public function marketingBullets(): array
    {
        $features = $this->features ?? [];

        if (isset($features['bullets']) && is_array($features['bullets'])) {
            return array_values(array_filter(array_map('strval', $features['bullets'])));
        }

        $bullets = [];
        foreach ($features as $item) {
            if (is_string($item) && $item !== '') {
                $bullets[] = $item;
            }
        }

        return $bullets;
    }
}