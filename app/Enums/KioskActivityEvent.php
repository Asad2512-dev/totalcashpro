<?php

declare(strict_types=1);

namespace App\Enums;

enum KioskActivityEvent: string
{
    case ClockIn = 'clock_in';
    case ClockOut = 'clock_out';
    case StartBreak = 'start_break';
    case EndBreak = 'end_break';
    case PinFailed = 'pin_failed';
    case KioskStarted = 'kiosk_started';
    case KioskClosed = 'kiosk_closed';
    case ForceLogout = 'force_logout';
    case KioskReset = 'kiosk_reset';
    case TokenRegenerated = 'token_regenerated';
    case RotaRestricted = 'rota_restricted';
    case RotaOverride = 'rota_override';
    case KioskDisabled = 'kiosk_disabled';
    case BranchChanged = 'branch_changed';
    case SettingsChanged = 'settings_changed';

    public function label(): string
    {
        return match ($this) {
            self::ClockIn => 'Clock In',
            self::ClockOut => 'Clock Out',
            self::StartBreak => 'Start Break',
            self::EndBreak => 'End Break',
            self::PinFailed => 'Failed PIN',
            self::KioskStarted => 'Kiosk Started',
            self::KioskClosed => 'Kiosk Closed',
            self::ForceLogout => 'Force Logout',
            self::KioskReset => 'Kiosk Reset',
            self::TokenRegenerated => 'Token Regenerated',
            self::RotaRestricted => 'Rota Restricted',
            self::RotaOverride => 'Rota Override',
            self::KioskDisabled => 'Kiosk Disabled',
            self::BranchChanged => 'Branch Changed',
            self::SettingsChanged => 'Settings Changed',
        };
    }
}
