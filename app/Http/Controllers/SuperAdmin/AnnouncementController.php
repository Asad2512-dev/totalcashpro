<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Organization;
use App\Services\SuperAdmin\ContentManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class AnnouncementController extends Controller
{
    public function __construct(private readonly ContentManagementService $content) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Create Announcement',
            'active' => 'announcements',
            'action' => route('super-admin.announcements.store'),
            'cancelRoute' => route('super-admin.announcements'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->content->saveAnnouncement($this->validated($request));

        return redirect()->route('super-admin.announcements')->with('status', 'Announcement created.');
    }

    public function edit(Announcement $announcement): View
    {
        return view('admin.crud.form', [
            'title' => 'Edit Announcement',
            'active' => 'announcements',
            'action' => route('super-admin.announcements.update', $announcement),
            'method' => 'PUT',
            'cancelRoute' => route('super-admin.announcements'),
            'model' => $announcement,
            'fields' => $this->fields($announcement),
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $this->content->saveAnnouncement($this->validated($request), $announcement);

        return redirect()->route('super-admin.announcements')->with('status', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->route('super-admin.announcements')->with('status', 'Announcement deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'audience' => ['required', 'string', 'max:50'],
            'channel' => ['required', 'string', 'max:50'],
            'target_plan_slug' => ['nullable', 'string', 'max:50'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'status' => ['required', 'in:draft,published,archived'],
            'scheduled_at' => ['nullable', 'date'],
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fields(?Announcement $item = null): array
    {
        return [
            ['name' => 'title', 'value' => $item?->title, 'full' => true],
            ['name' => 'body', 'type' => 'textarea', 'value' => $item?->body, 'full' => true],
            ['name' => 'audience', 'type' => 'select', 'value' => $item?->audience ?? 'everyone', 'options' => [
                'everyone' => 'Everyone',
                'basic' => 'Basic plan',
                'professional' => 'Professional plan',
                'organization' => 'Specific organisation',
            ]],
            ['name' => 'channel', 'type' => 'select', 'value' => $item?->channel ?? 'in_app', 'options' => ['in_app' => 'In-app', 'email' => 'Email', 'both' => 'Both']],
            ['name' => 'target_plan_slug', 'label' => 'Plan target', 'value' => $item?->target_plan_slug],
            ['name' => 'organization_id', 'type' => 'select', 'label' => 'Organisation', 'value' => $item?->organization_id, 'options' => ['' => '—'] + Organization::query()->pluck('name', 'id')->all()],
            ['name' => 'scheduled_at', 'type' => 'datetime-local', 'value' => $item?->scheduled_at?->format('Y-m-d\TH:i')],
            ['name' => 'status', 'type' => 'select', 'value' => $item?->status->value ?? 'draft', 'options' => ['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']],
        ];
    }
}
