<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AttendanceLogType;
use App\Enums\AttendanceSource;
use App\Enums\BreakType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'user_id',
        'branch_kiosk_id',
        'type',
        'source',
        'logged_at',
        'idempotency_key',
    ];

    protected function casts(): array
    {
        return [
            'type' => AttendanceLogType::class,
            'source' => AttendanceSource::class,
            'logged_at' => 'datetime',
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
