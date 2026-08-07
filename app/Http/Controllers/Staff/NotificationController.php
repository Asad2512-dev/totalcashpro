<?php

declare(strict_types=1);

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = AppNotification::query()
            ->where('user_id', $request->user()->id)
            ->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        return view('staff.notifications.index', [
            'notifications' => $query->paginate(20)->withQueryString(),
            'categories' => \App\Enums\NotificationCategory::cases(),
            'activeCategory' => $request->input('category'),
        ]);
    }

    public function markRead(Request $request): RedirectResponse
    {
        AppNotification::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('status', 'Notifications marked as read.');
    }
}
