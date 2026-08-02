<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\Logging\ActivityLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

final class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();

        return view('admin.crud.form', [
            'title' => 'Edit Profile',
            'active' => 'profile',
            'action' => route('super-admin.profile.update'),
            'method' => 'PUT',
            'cancelRoute' => route('super-admin.profile'),
            'model' => $user,
            'fields' => [
                ['name' => 'name', 'value' => $user?->name],
                ['name' => 'email', 'type' => 'email', 'value' => $user?->email],
                ['name' => 'phone', 'value' => $user?->phone],
                ['name' => 'password', 'type' => 'password', 'label' => 'New password (optional)'],
                ['name' => 'password_confirmation', 'type' => 'password', 'label' => 'Confirm password'],
            ],
        ]);
    }

    public function update(Request $request, ActivityLogger $activity): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        unset($data['password_confirmation']);

        $user->update($data);
        $activity->log('profile.updated', 'Profile updated', $user, $user);

        return redirect()->route('super-admin.profile')->with('status', 'Profile updated.');
    }
}
