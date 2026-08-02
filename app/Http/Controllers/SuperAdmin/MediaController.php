<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Services\SuperAdmin\ContentManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class MediaController extends Controller
{
    public function __construct(private readonly ContentManagementService $content) {}

    public function create(): View
    {
        return view('admin.crud.form', [
            'title' => 'Upload Media',
            'active' => 'media',
            'action' => route('super-admin.media.store'),
            'cancelRoute' => route('super-admin.media'),
            'fields' => [
                ['name' => 'file', 'type' => 'file', 'label' => 'File', 'full' => true],
                ['name' => 'folder', 'label' => 'Folder', 'value' => 'general'],
                ['name' => 'collection', 'label' => 'Collection', 'value' => 'library'],
                ['name' => 'alt', 'label' => 'Alt text'],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'folder' => ['nullable', 'string', 'max:100'],
            'collection' => ['nullable', 'string', 'max:100'],
            'alt' => ['nullable', 'string', 'max:255'],
        ]);

        $asset = $this->content->uploadMedia(
            $request->file('file'),
            $request->string('folder')->toString() ?: null,
            $request->string('collection')->toString() ?: null,
        );

        if ($request->filled('alt')) {
            $asset->update(['alt' => $request->string('alt')->toString()]);
        }

        return redirect()->route('super-admin.media')->with('status', 'File uploaded.');
    }

    public function destroy(MediaAsset $medium): RedirectResponse
    {
        $this->content->deleteMedia($medium);

        return redirect()->route('super-admin.media')->with('status', 'File deleted.');
    }
}
