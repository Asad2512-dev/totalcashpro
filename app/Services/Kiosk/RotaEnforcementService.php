<?php

declare(strict_types=1);

namespace App\Services\Kiosk;

use App\Contracts\ServiceInterface;
use App\Enums\RotaEnforcementMode;
use App\Models\BranchKiosk;
use App\Models\RotaShift;
use App\Models\User;
use Illuminate\Support\Carbon;

final class RotaEnforcementService implements ServiceInterface
{
    /**
     * @return array{allowed: bool, mode: string, message: ?string, minutes_delta: ?int, shift: ?RotaShift}
     */
    public function evaluateClockIn(User $staff, BranchKiosk $kiosk, KioskConfigurationService $config): array
    {
        $mode = $config->rotaMode($kiosk);

        if ($mode === RotaEnforcementMode::Disabled) {
            return ['allowed' => true, 'mode' => $mode->value, 'message' => null, 'minutes_delta' => null, 'shift' => null];
        }

        $shift = $this->publishedShiftForToday($staff);

        if ($shift === null) {
            $message = 'You are not scheduled to work today.';

            return [
                'allowed' => $mode !== RotaEnforcementMode::Strict,
                'mode' => $mode->value,
                'message' => $message,
                'minutes_delta' => null,
                'shift' => null,
            ];
        }

        $settings = $config->forKiosk($kiosk);
        $early = (int) ($settings['early_clock_in_minutes'] ?? 15);
        $late = (int) ($settings['late_clock_in_minutes'] ?? 15);
        $now = now();
        $windowStart = $shift->start_time->copy()->subMinutes($early);
        $windowEnd = $shift->start_time->copy()->addMinutes($late);

        if ($now->between($windowStart, $windowEnd)) {
            return ['allowed' => true, 'mode' => $mode->value, 'message' => null, 'minutes_delta' => null, 'shift' => $shift];
        }

        $minutesDelta = $now->lt($windowStart)
            ? (int) $now->diffInMinutes($windowStart)
            : (int) $windowEnd->diffInMinutes($now);

        $message = $now->lt($windowStart)
            ? "You are {$minutesDelta} minutes early."
            : "You are {$minutesDelta} minutes late.";

        return [
            'allowed' => $mode !== RotaEnforcementMode::Strict,
            'mode' => $mode->value,
            'message' => $message,
            'minutes_delta' => $minutesDelta,
            'shift' => $shift,
        ];
    }

    public function publishedShiftForToday(User $staff): ?RotaShift
    {
        return RotaShift::query()
            ->where('user_id', $staff->id)
            ->whereDate('shift_date', now()->toDateString())
            ->whereHas('rotaVersion', fn ($q) => $q->whereIn('status', ['published', 'locked']))
            ->orderBy('start_time')
            ->first();
    }
}
