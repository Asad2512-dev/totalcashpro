<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ShiftSwapRequest extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'requester_id', 'target_user_id', 'rota_shift_id',
        'status', 'reason', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RequestStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function rotaShift(): BelongsTo
    {
        return $this->belongsTo(RotaShift::class);
    }
}
