<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AttendanceSource;
use App\Enums\BreakType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AttendanceBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'user_id',
        'break_type',
        'kiosk_break_type_id',
        'break_started_at',
        'break_ended_at',
        'status',
        'is_paid',
        'planned_minutes',
        'source',
        'branch_kiosk_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'break_type' => BreakType::class,
            'source' => AttendanceSource::class,
            'break_started_at' => 'datetime',
            'break_ended_at' => 'datetime',
            'is_paid' => 'boolean',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branchKiosk(): BelongsTo
    {
        return $this->belongsTo(BranchKiosk::class);
    }
}
