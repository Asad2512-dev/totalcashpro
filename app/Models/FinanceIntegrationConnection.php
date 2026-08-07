<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FinanceIntegrationProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class FinanceIntegrationConnection extends Model
{
    protected $fillable = [
        'organization_id',
        'provider',
        'status',
        'external_account_id',
        'settings',
        'connected_at',
    ];

    protected function casts(): array
    {
        return [
            'provider' => FinanceIntegrationProvider::class,
            'settings' => 'array',
            'connected_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
