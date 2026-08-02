<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class CmsTestimonial extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name', 'role', 'business', 'quote', 'is_featured', 'sort_order', 'status',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'status' => PublishStatus::class,
        ];
    }
}