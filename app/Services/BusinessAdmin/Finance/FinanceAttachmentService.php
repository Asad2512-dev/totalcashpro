<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin\Finance;

use App\Contracts\ServiceInterface;
use App\Models\User;
use App\Repositories\Contracts\FinanceAttachmentRepositoryInterface;
use App\Services\BusinessAdmin\BranchContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class FinanceAttachmentService implements ServiceInterface
{
    public function __construct(
        private readonly FinanceAttachmentRepositoryInterface $attachments,
        private readonly BranchContext $branchContext,
    ) {}

    public function store(User $user, Model $model, UploadedFile $file): void
    {
        $orgId = (int) $user->organization_id;
        $path = $file->store("finance/{$orgId}", 'public');

        $this->attachments->attachTo($model, [
            'organization_id' => $orgId,
            'disk' => 'public',
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize() ?: 0,
            'uploaded_by' => $user->id,
        ]);
    }

    public function delete(User $user, int $attachmentId): void
    {
        $attachment = $this->attachments->findOrFail($attachmentId);

        if ((int) $attachment->organization_id !== (int) $user->organization_id) {
            abort(403);
        }

        Storage::disk($attachment->disk)->delete($attachment->path);
        $this->attachments->delete($attachmentId);
    }
}
