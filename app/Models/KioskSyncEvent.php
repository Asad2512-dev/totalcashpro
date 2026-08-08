<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class KioskSyncEvent extends Model
{
    protected $fillable = [
        'organization_id',
        'branch_id',
        'branch_kiosk_id',
        'user_id',
        'event_type',
        'idempotency_key',
        'client_sequence',
        'event_time',
        'sync_status',
        'payload',
        'result',
    ];

    protected function casts(): array
    {
        return [
            'event_time' => 'datetime',
            'payload' => 'array',
            'result' => 'array',
        ];
    }

    public function kiosk(): BelongsTo
    {
        return $this->belongsTo(BranchKiosk::class, 'branch_kiosk_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
