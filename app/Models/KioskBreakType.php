<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class KioskBreakType extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'description',
        'is_paid',
        'max_duration_minutes',
        'is_active',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (KioskBreakType $type): void {
            if ($type->slug === null || $type->slug === '') {
                $type->slug = Str::slug($type->name);
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
