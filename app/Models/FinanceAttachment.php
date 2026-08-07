<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

final class FinanceAttachment extends Model
{
    protected $fillable = [
        'organization_id',
        'attachable_type',
        'attachable_id',
        'disk',
        'path',
        'filename',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): ?string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
