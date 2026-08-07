<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SecurityLogEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SecurityLog extends Model
{
    protected $fillable = [
        'user_id',
        'event',
        'description',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'event' => SecurityLogEvent::class,
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
