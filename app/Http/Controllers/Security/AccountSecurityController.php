<?php

declare(strict_types=1);

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use App\Enums\OtpPurpose;
use App\Enums\SecurityLogEvent;
use App\Services\Security\DeviceSessionService;
use App\Services\Security\LoginHistoryService;
use App\Services\Security\OtpService;
use App\Services\Security\NotificationPreferenceService;
use App\Services\Security\PasswordService;
use App\Services\Security\ProfileEmailService;
use App\Services\Security\SecurityLogService;
use App\Services\Security\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

final class AccountSecurityController extends Controller
{
    public function __construct(
        private readonly TwoFactorService $twoFactorService,
        private readonly OtpService $otpService,
        private readonly LoginHistoryService $loginHistoryService,
        private readonly DeviceSessionService $deviceSessionService,
        private readonly SecurityLogService $securityLogService,
        private readonly PasswordService $passwordService,
        private readonly ProfileEmailService $profileEmailService,
        private readonly NotificationPreferenceService $notificationPreferences,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view($this->viewPrefix($user).'security.index', [
            'user' => $user,
            'loginHistories' => $this->loginHistoryService->forUser($user, 10),
            'devices' => $this->deviceSessionService->activeForUser($user),
            'securityLogs' => $this->securityLogService->forUser($user, 20),
            'twoFactorEnabled' => $this->twoFactorService->isEnabled($user),
            'notificationPrefs' => $this->notificationPreferences->allForUser($user),
        ]);
    }

    public function enableTwoFactor(Request $request): RedirectResponse
    {
        $this->otpService->generateAndSend($request->user(), OtpPurpose::TwoFactorSetup, $request);

        return back()->with('status', 'Enter the verification code sent to your email to enable two-factor authentication.');
    }

    public function confirmTwoFactor(Request $request): RedirectResponse
    {
        $request->validate(['otp' => ['required', 'string', 'size:6']]);

        $codes = $this->twoFactorService->enableWithEmail(
            $request->user(),
            $request->string('otp')->toString(),
            $request,
        );

        return back()
            ->with('success', 'Two-factor authentication enabled.')
            ->with('recovery_codes', $codes->all());
    }

    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'string']]);

        if (! \Illuminate\Support\Facades\Hash::check($request->string('password')->toString(), $request->user()->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        $this->twoFactorService->disable($request->user(), $request);

        return back()->with('success', 'Two-factor authentication disabled.');
    }

    public function trustDevice(Request $request, UserDevice $device): RedirectResponse
    {
        abort_unless($device->user_id === $request->user()->id, 403);

        $this->deviceSessionService->markTrusted($device);

        $this->securityLogService->log(
            SecurityLogEvent::DeviceTrusted,
            $request->user(),
            'Device marked as trusted: '.$device->device_name,
            $request,
        );

        return back()->with('success', 'Device marked as trusted.');
    }

    public function logoutDevice(Request $request, UserDevice $device): RedirectResponse
    {
        abort_unless($device->user_id === $request->user()->id, 403);

        $this->deviceSessionService->logoutDevice($device);

        $this->securityLogService->log(
            SecurityLogEvent::DeviceRemoved,
            $request->user(),
            'Device logged out: '.$device->device_name,
            $request,
        );

        return back()->with('success', 'Device signed out.');
    }

    public function logoutAllDevices(Request $request): RedirectResponse
    {
        $count = $this->deviceSessionService->logoutAllExceptCurrent($request->user(), $request);

        $this->securityLogService->log(
            SecurityLogEvent::AllDevicesLoggedOut,
            $request->user(),
            "Signed out {$count} other device(s)",
            $request,
        );

        return back()->with('success', 'All other devices have been signed out.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => $this->passwordService->rules(),
        ]);

        $this->passwordService->update(
            $request->user(),
            $validated['current_password'],
            $validated['password'],
            $request,
        );

        return back()->with('success', 'Password updated successfully.');
    }

    public function requestEmailChange(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$request->user()->id],
        ]);

        $this->profileEmailService->requestChange(
            $request->user(),
            $validated['email'],
            $request,
        );

        return back()->with('status', 'Enter the verification code sent to your current email to confirm the change.');
    }

    public function confirmEmailChange(Request $request): RedirectResponse
    {
        $request->validate(['otp' => ['required', 'string', 'size:6']]);

        $this->profileEmailService->confirmChange(
            $request->user(),
            $request->string('otp')->toString(),
            $request,
        );

        return back()->with('success', 'Email updated. Please verify your new email address.');
    }

    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'preferences' => ['required', 'array'],
        ]);

        $this->notificationPreferences->sync($request->user(), $validated['preferences']);

        return back()->with('success', 'Notification preferences saved.');
    }

    private function viewPrefix(\App\Models\User $user): string
    {
        return match (true) {
            $user->isSuperAdmin() => 'super-admin.',
            $user->isStaff() => 'staff.',
            default => 'business-admin.',
        };
    }
}
