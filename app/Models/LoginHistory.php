<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LoginHistory extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'role',
        'ip_address',
        'browser',
        'device',
        'operating_system',
        'country',
        'success',
        'failure_reason',
        'event_type',
        'logged_in_at',
    ];

    protected function casts(): array
    {
        return [
            'success' => 'boolean',
            'logged_in_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
