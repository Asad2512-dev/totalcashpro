<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LeaveType;
use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id', 'organization_id', 'branch_id', 'type', 'start_date', 'end_date',
        'status', 'reason', 'admin_notes', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => LeaveType::class,
            'status' => RequestStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
