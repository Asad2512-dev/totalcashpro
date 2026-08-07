<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class KioskSession extends Model
{
    protected $fillable = [
        'branch_kiosk_id',
        'session_token',
        'started_by_user_id',
        'ended_by_user_id',
        'ip_address',
        'user_agent',
        'device_summary',
        'started_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function kiosk(): BelongsTo
    {
        return $this->belongsTo(BranchKiosk::class, 'branch_kiosk_id');
    }

    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by_user_id');
    }

    public function endedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ended_by_user_id');
    }

    public function isActive(): bool
    {
        return $this->ended_at === null;
    }
}
