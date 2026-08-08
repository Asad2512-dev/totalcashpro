<?php

declare(strict_types=1);

namespace App\Services\Kiosk;

use App\Contracts\ServiceInterface;
use App\Enums\AttendanceSource;
use App\Enums\BreakType;
use App\Enums\KioskSyncEventType;
use App\Models\AttendanceBreak;
use App\Models\BranchKiosk;
use App\Models\KioskSyncEvent;
use App\Models\User;
use Illuminate\Support\Str;

final class KioskSyncEventService implements ServiceInterface
{
    public function __construct(private readonly KioskAttendanceService $attendance) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function process(
        BranchKiosk $kiosk,
        User $staff,
        KioskSyncEventType $type,
        string $idempotencyKey,
        array $payload = [],
    ): array {
        $existing = KioskSyncEvent::query()
            ->where('branch_kiosk_id', $kiosk->id)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if ($existing !== null && is_array($existing->result)) {
            return $existing->result;
        }

        $eventTime = isset($payload['event_time'])
            ? \Illuminate\Support\Carbon::parse($payload['event_time'])
            : now();

        $result = match ($type) {
            KioskSyncEventType::ClockIn => $this->attendance->clockIn($kiosk, $staff, $idempotencyKey, (bool) ($payload['rota_override'] ?? false)),
            KioskSyncEventType::ClockOut => $this->attendance->clockOut($kiosk, $staff, $idempotencyKey),
            KioskSyncEventType::BreakStart => $this->attendance->startBreak(
                $kiosk,
                $staff,
                (string) ($payload['break_type'] ?? BreakType::Other->value),
                $idempotencyKey,
            ),
            KioskSyncEventType::BreakEnd => $this->attendance->endBreak($kiosk, $staff, $idempotencyKey),
        };

        KioskSyncEvent::query()->updateOrCreate(
            [
                'branch_kiosk_id' => $kiosk->id,
                'idempotency_key' => $idempotencyKey,
            ],
            [
                'organization_id' => $kiosk->organization_id,
                'branch_id' => $kiosk->branch_id,
                'user_id' => $staff->id,
                'event_type' => $type->value,
                'client_sequence' => $payload['client_sequence'] ?? null,
                'event_time' => $eventTime,
                'sync_status' => 'synced',
                'payload' => $payload,
                'result' => $result,
            ],
        );

        return $result;
    }
}
