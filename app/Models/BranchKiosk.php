<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class BranchKiosk extends Model
{
    protected $fillable = [
        'organization_id',
        'branch_id',
        'name',
        'token',
        'welcome_message',
        'show_photos',
        'is_enabled',
        'last_started_at',
    ];

    protected function casts(): array
    {
        return [
            'show_photos' => 'boolean',
            'is_enabled' => 'boolean',
            'last_started_at' => 'datetime',
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

    public function sessions(): HasMany
    {
        return $this->hasMany(KioskSession::class);
    }

    public function activeSession(): HasOne
    {
        return $this->hasOne(KioskSession::class)->whereNull('ended_at')->latestOfMany('started_at');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(KioskActivityLog::class);
    }

    public function publicUrl(): string
    {
        return url('/kiosk/'.$this->token);
    }
}
