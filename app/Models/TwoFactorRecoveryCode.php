<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TwoFactorRecoveryCode extends Model
{
    protected $fillable = [
        'user_id',
        'code_hash',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
