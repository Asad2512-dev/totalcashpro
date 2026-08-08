<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RotaVersionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class RotaVersion extends Model
{
    protected $fillable = [
        'organization_id',
        'branch_id',
        'week_start',
        'version_number',
        'status',
        'notes',
        'created_by_user_id',
        'finalized_by_user_id',
        'published_by_user_id',
        'finalized_at',
        'published_at',
        'locked_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'status' => RotaVersionStatus::class,
            'finalized_at' => 'datetime',
            'published_at' => 'datetime',
            'locked_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(RotaShift::class);
    }

    public function amendments(): HasMany
    {
        return $this->hasMany(RotaAmendment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by_user_id');
    }

    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by_user_id');
    }

    public function weekEnd(): \Illuminate\Support\Carbon
    {
        return $this->week_start->copy()->endOfWeek();
    }

    public function weekLabel(): string
    {
        return $this->week_start->format('d M').' – '.$this->weekEnd()->format('d M Y');
    }
}
