<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Coupon extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code', 'type', 'value', 'max_uses', 'used_count', 'starts_at', 'expires_at', 'status', 'plan_id', 'organization_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => CouponType::class,
            'value' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function discountLabel(): string
    {
        return $this->type === CouponType::Percentage
            ? rtrim(rtrim(number_format((float) $this->value, 2), '0'), '.').'%'
            : '£'.number_format((float) $this->value, 2);
    }

    public function usageLabel(): string
    {
        $max = $this->max_uses === null ? '∞' : (string) $this->max_uses;

        return $this->used_count.' / '.$max;
    }
}