<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class CmsPage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'content', 'status', 'meta', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PublishStatus::class,
            'meta' => 'array',
            'published_at' => 'datetime',
        ];
    }
}