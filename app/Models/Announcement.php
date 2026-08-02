<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Announcement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title', 'body', 'audience', 'channel', 'target_plan_slug', 'organization_id', 'status', 'scheduled_at', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PublishStatus::class,
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }
}