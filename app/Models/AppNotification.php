<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AppNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'title', 'body', 'type', 'category', 'priority', 'read_at', 'archived_at', 'data',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'archived_at' => 'datetime',
            'data' => 'array',
            'category' => NotificationCategory::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}