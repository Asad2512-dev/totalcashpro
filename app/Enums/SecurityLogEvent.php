<?php

declare(strict_types=1);

namespace App\Enums;

enum SecurityLogEvent: string
{
    case PasswordChanged = 'password_changed';
    case TwoFactorEnabled = 'two_factor_enabled';
    case TwoFactorDisabled = 'two_factor_disabled';
    case LoginSuccess = 'login_success';
    case LoginFailure = 'login_failure';
    case DeviceRemoved = 'device_removed';
    case DeviceTrusted = 'device_trusted';
    case EmailChanged = 'email_changed';
    case PermissionsChanged = 'permissions_changed';
    case OtpRequested = 'otp_requested';
    case OtpVerified = 'otp_verified';
    case RecoveryCodeUsed = 'recovery_code_used';
    case AllDevicesLoggedOut = 'all_devices_logged_out';
    case ApiTokenCreated = 'api_token_created';
    case ApiTokenRevoked = 'api_token_revoked';
    case Logout = 'logout';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
