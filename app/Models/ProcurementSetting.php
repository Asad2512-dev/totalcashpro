<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ProcurementSetting extends Model
{
    protected $fillable = [
        'organization_id',
        'quantity_tolerance_percent',
        'price_tolerance_percent',
        'auto_create_bill_on_match',
    ];

    protected function casts(): array
    {
        return [
            'quantity_tolerance_percent' => 'decimal:2',
            'price_tolerance_percent' => 'decimal:2',
            'auto_create_bill_on_match' => 'boolean',
        ];
    }

    public static function forOrganization(int $organizationId): self
    {
        return self::query()->firstOrCreate(
            ['organization_id' => $organizationId],
            [
                'quantity_tolerance_percent' => 2,
                'price_tolerance_percent' => 1,
                'auto_create_bill_on_match' => true,
            ],
        );
    }
}
