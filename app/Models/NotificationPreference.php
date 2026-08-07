<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NotificationCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'email_enabled',
        'database_enabled',
    ];

    protected function casts(): array
    {
        return [
            'category' => NotificationCategory::class,
            'email_enabled' => 'boolean',
            'database_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
