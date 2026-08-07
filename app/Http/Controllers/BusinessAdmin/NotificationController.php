<?php

declare(strict_types=1);

namespace App\Http\Controllers\BusinessAdmin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = AppNotification::query()
            ->where('user_id', $user->id)
            ->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        return view('business-admin.notifications.index', [
            'notifications' => $query->paginate(20)->withQueryString(),
            'categories' => \App\Enums\NotificationCategory::cases(),
            'activeCategory' => $request->input('category'),
        ]);
    }

    public function markRead(Request $request, int $notificationId): RedirectResponse
    {
        $user = $request->user();
        
        AppNotification::query()
            ->where('id', $notificationId)
            ->where('user_id', $user->id)
            ->update(['read_at' => now()]);

        return redirect()
            ->route('business-admin.notifications')
            ->with('success', 'Notification marked as read.');
    }
}
