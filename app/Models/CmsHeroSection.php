<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class CmsHeroSection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'page_key', 'eyebrow', 'headline', 'subheadline', 'primary_cta_label',
        'primary_cta_url', 'secondary_cta_label', 'secondary_cta_url',
        'media_path', 'status', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => PublishStatus::class,
        ];
    }
}