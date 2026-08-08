<?php

declare(strict_types=1);

namespace App\Services\Kiosk;

use App\Contracts\ServiceInterface;
use App\Enums\AttendanceSource;
use App\Enums\BreakType;
use App\Models\AttendanceBreak;
use App\Models\BranchKiosk;
use App\Models\KioskBreakType;
use App\Models\User;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Support\Kiosk\KioskContext;

final class KioskBreakService implements ServiceInterface
{
    public function __construct(private readonly AttendanceRepositoryInterface $attendance) {}

    public function activeBreak(User $staff): ?AttendanceBreak
    {
        return AttendanceBreak::query()
            ->where('user_id', $staff->id)
            ->whereDate('break_started_at', now()->toDateString())
            ->whereNull('break_ended_at')
            ->latest('break_started_at')
            ->first();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public function start(
        BranchKiosk $kiosk,
        User $staff,
        BreakType $type,
        array $config,
        ?string $idempotencyKey = null,
    ): AttendanceBreak {
        return $this->attendance->createBreak([
            'organization_id' => $kiosk->organization_id,
            'branch_id' => $kiosk->branch_id,
            'user_id' => $staff->id,
            'break_type' => $type->value,
            'break_started_at' => now(),
            'break_ended_at' => null,
            'is_paid' => (bool) ($config['paid'] ?? false),
            'planned_minutes' => (int) ($config['default_minutes'] ?? 15),
            'source' => AttendanceSource::Kiosk->value,
            'branch_kiosk_id' => $kiosk->id,
            'status' => 'active',
        ]);
    }

    public function startForContext(KioskContext $context, User $staff, KioskBreakType $type): AttendanceBreak
    {
        $legacy = app(KioskBreakTypeService::class)->legacyEnumFor($type);

        return $this->attendance->createBreak([
            'organization_id' => $context->organizationId,
            'branch_id' => $context->branchId,
            'user_id' => $staff->id,
            'break_type' => $legacy->value,
            'kiosk_break_type_id' => $type->id,
            'break_started_at' => now(),
            'break_ended_at' => null,
            'is_paid' => $type->is_paid,
            'planned_minutes' => $type->max_duration_minutes ?? 15,
            'source' => AttendanceSource::Kiosk->value,
            'branch_kiosk_id' => null,
            'status' => 'active',
        ]);
    }

    public function end(AttendanceBreak $break): AttendanceBreak
    {
        $break->update([
            'break_ended_at' => now(),
            'status' => 'completed',
        ]);

        return $break->fresh();
    }

    public function durationMinutes(AttendanceBreak $break): int
    {
        if ($break->break_started_at === null) {
            return 0;
        }

        $end = $break->break_ended_at ?? now();

        return (int) $break->break_started_at->diffInMinutes($end);
    }
}
