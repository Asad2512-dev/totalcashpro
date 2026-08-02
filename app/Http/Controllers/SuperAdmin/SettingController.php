<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\ContentManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SettingController extends Controller
{
    public function __construct(private readonly ContentManagementService $content) {}

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['array'],
            'settings.*.*' => ['nullable', 'string'],
        ]);

        $this->content->saveSettings($data['settings']);

        return back()->with('status', 'Settings saved.');
    }
}
