<?php

declare(strict_types=1);

namespace App\Support\Kiosk;

use App\Models\KioskSession;
use App\Models\OrganizationKioskSetting;

final class KioskContext
{
    /**
     * @param  array<string, mixed>  $settings
     */
    public function __construct(
        public readonly int $organizationId,
        public readonly int $branchId,
        public readonly array $settings,
        public readonly ?KioskSession $session = null,
        public readonly ?OrganizationKioskSetting $kioskSettings = null,
    ) {}

    public function showAttendanceList(): bool
    {
        return (bool) ($this->kioskSettings?->show_attendance_list ?? true);
    }

    public function displayName(): string
    {
        return (string) ($this->kioskSettings?->display_name ?: 'Staff Clock');
    }
}
