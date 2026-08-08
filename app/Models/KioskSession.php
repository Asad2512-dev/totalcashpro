<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\KioskSessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class KioskSession extends Model
{
    protected $fillable = [
        'branch_kiosk_id',
        'organization_id',
        'branch_id',
        'session_token',
        'status',
        'started_by_user_id',
        'ended_by_user_id',
        'revoked_by_user_id',
        'ip_address',
        'user_agent',
        'device_summary',
        'started_at',
        'last_activity_at',
        'ended_at',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => KioskSessionStatus::class,
            'started_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'ended_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function kiosk(): BelongsTo
    {
        return $this->belongsTo(BranchKiosk::class, 'branch_kiosk_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by_user_id');
    }

    public function endedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ended_by_user_id');
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by_user_id');
    }

    public function isActive(): bool
    {
        if ($this->ended_at !== null) {
            return false;
        }

        $status = $this->status instanceof KioskSessionStatus
            ? $this->status
            : KioskSessionStatus::tryFrom((string) $this->status);

        return $status === null || $status === KioskSessionStatus::Active;
    }
}
