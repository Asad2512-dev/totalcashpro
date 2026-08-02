<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\User;
use App\Services\SuperAdmin\ContentManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class NotificationController extends Controller
{
    public function __construct(private readonly ContentManagementService $content) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Create Notification',
            'active' => 'notifications',
            'action' => route('super-admin.notifications.store'),
            'cancelRoute' => route('super-admin.notifications'),
            'fields' => [
                ['name' => 'user_id', 'type' => 'select', 'label' => 'User', 'options' => User::query()->orderBy('name')->pluck('name', 'id')->all()],
                ['name' => 'title'],
                ['name' => 'body', 'type' => 'textarea', 'full' => true],
                ['name' => 'type', 'type' => 'select', 'value' => 'info', 'options' => ['info' => 'Info', 'alert' => 'Alert', 'success' => 'Success']],
                ['name' => 'priority', 'type' => 'select', 'value' => 'normal', 'options' => ['low' => 'Low', 'normal' => 'Normal', 'high' => 'High']],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:50'],
            'priority' => ['required', 'string', 'max:50'],
        ]);

        $this->content->createNotification($data);

        return redirect()->route('super-admin.notifications')->with('status', 'Notification created.');
    }

    public function read(AppNotification $notification): RedirectResponse
    {
        $this->content->markNotificationRead($notification);

        return back()->with('status', 'Marked as read.');
    }

    public function archive(AppNotification $notification): RedirectResponse
    {
        $this->content->archiveNotification($notification);

        return back()->with('status', 'Notification archived.');
    }

    public function destroy(AppNotification $notification): RedirectResponse
    {
        $this->content->deleteNotification($notification);

        return back()->with('status', 'Notification deleted.');
    }
}
