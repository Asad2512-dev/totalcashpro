<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class UserDevice extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'device_name',
        'browser',
        'operating_system',
        'ip_address',
        'is_trusted',
        'is_current',
        'last_active_at',
        'logged_out_at',
    ];

    protected function casts(): array
    {
        return [
            'is_trusted' => 'boolean',
            'is_current' => 'boolean',
            'last_active_at' => 'datetime',
            'logged_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->logged_out_at === null;
    }
}
