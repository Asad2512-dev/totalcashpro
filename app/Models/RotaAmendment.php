<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RotaAmendment extends Model
{
    protected $fillable = [
        'rota_version_id',
        'organization_id',
        'branch_id',
        'user_id',
        'rota_shift_id',
        'field',
        'old_value',
        'new_value',
        'reason',
        'amended_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'old_value' => 'array',
            'new_value' => 'array',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(RotaVersion::class, 'rota_version_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(RotaShift::class, 'rota_shift_id');
    }

    public function amendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'amended_by_user_id');
    }
}
