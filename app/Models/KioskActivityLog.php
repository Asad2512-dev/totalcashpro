<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class KioskActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'branch_kiosk_id',
        'organization_id',
        'branch_id',
        'event',
        'staff_user_id',
        'actor_user_id',
        'ip_address',
        'user_agent',
        'device_summary',
        'meta',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function kiosk(): BelongsTo
    {
        return $this->belongsTo(BranchKiosk::class, 'branch_kiosk_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
