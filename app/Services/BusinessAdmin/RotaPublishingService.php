<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\RotaVersionStatus;
use App\Models\RotaAmendment;
use App\Models\RotaVersion;
use App\Models\User;
use App\Services\Logging\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class RotaPublishingService implements ServiceInterface
{
    public function __construct(
        private readonly RotaVersionService $versions,
        private readonly RotaValidationService $validation,
        private readonly RotaNotificationService $notifications,
        private readonly AuditLogger $audit,
    ) {}

    public function finalize(User $admin, RotaVersion $version, Request $request): RotaVersion
    {
        $this->assertOrg($admin, $version);
        $this->assertTransition($version, RotaVersionStatus::Finalized);

        $this->validation->assertCanFinalize($version);

        $version->update([
            'status' => RotaVersionStatus::Finalized,
            'finalized_by_user_id' => $admin->id,
            'finalized_at' => now(),
        ]);

        $this->audit->log('rota.finalized', $admin, $version, null, [
            'version' => $version->version_number,
            'week' => $version->week_start?->toDateString(),
        ], $request);

        return $version->refresh();
    }

    public function publish(User $admin, RotaVersion $version, Request $request): RotaVersion
    {
        $this->assertOrg($admin, $version);

        if ($version->status === RotaVersionStatus::Draft) {
            $version = $this->finalize($admin, $version, $request);
        }

        $this->assertTransition($version, RotaVersionStatus::Published);
        $this->validation->assertCanFinalize($version);

        $previous = $this->versions->publishedVersion(
            (int) $version->organization_id,
            (int) $version->branch_id,
            $version->week_start,
        );

        return DB::transaction(function () use ($admin, $version, $previous, $request): RotaVersion {
            if ($previous !== null && (int) $previous->id !== (int) $version->id) {
                $previous->update([
                    'status' => RotaVersionStatus::Locked,
                    'locked_at' => now(),
                ]);
            }

            $version->update([
                'status' => RotaVersionStatus::Published,
                'published_by_user_id' => $admin->id,
                'published_at' => now(),
            ]);

            $version->shifts()->update(['status' => 'published']);

            $this->notifications->notifyPublished($version, $previous);
            $this->audit->log('rota.published', $admin, $version, null, [
                'version' => $version->version_number,
                'week' => $version->week_start?->toDateString(),
                'previous_version' => $previous?->version_number,
            ], $request);

            return $version->refresh();
        });
    }

    public function lock(User $admin, RotaVersion $version, Request $request): RotaVersion
    {
        $this->assertOrg($admin, $version);
        $this->assertTransition($version, RotaVersionStatus::Locked);

        $version->update([
            'status' => RotaVersionStatus::Locked,
            'locked_at' => now(),
        ]);

        $this->audit->log('rota.locked', $admin, $version, null, [
            'version' => $version->version_number,
        ], $request);

        return $version->refresh();
    }

    public function archive(User $admin, RotaVersion $version, Request $request): RotaVersion
    {
        $this->assertOrg($admin, $version);
        $this->assertTransition($version, RotaVersionStatus::Archived);

        $version->update([
            'status' => RotaVersionStatus::Archived,
            'archived_at' => now(),
        ]);

        $this->audit->log('rota.archived', $admin, $version, null, [
            'version' => $version->version_number,
        ], $request);

        return $version->refresh();
    }

    public function reopen(User $admin, RotaVersion $version, Request $request): RotaVersion
    {
        $this->assertOrg($admin, $version);

        if (! in_array($version->status, [RotaVersionStatus::Finalized, RotaVersionStatus::Locked], true)) {
            throw ValidationException::withMessages(['rota' => 'This version cannot be reopened.']);
        }

        $version->update(['status' => RotaVersionStatus::Draft]);

        $this->audit->log('rota.reopened', $admin, $version, null, [
            'version' => $version->version_number,
        ], $request);

        return $version->refresh();
    }

    public function unlockToDraft(User $admin, RotaVersion $version, string $reason, Request $request): RotaVersion
    {
        $this->assertOrg($admin, $version);

        if ($version->status !== RotaVersionStatus::Locked) {
            throw ValidationException::withMessages(['rota' => 'Only locked rotas can be unlocked.']);
        }

        $draft = $this->versions->resolveDraftVersion($admin, $version->week_start->toDateString());

        RotaAmendment::query()->create([
            'rota_version_id' => $draft->id,
            'organization_id' => $version->organization_id,
            'branch_id' => $version->branch_id,
            'field' => 'unlock',
            'old_value' => ['status' => $version->status->value],
            'new_value' => ['status' => RotaVersionStatus::Draft->value],
            'reason' => $reason,
            'amended_by_user_id' => $admin->id,
        ]);

        $this->audit->log('rota.unlocked', $admin, $version, ['status' => 'locked'], [
            'reason' => $reason,
            'draft_version' => $draft->version_number,
        ], $request);

        return $draft;
    }

    private function assertOrg(User $admin, RotaVersion $version): void
    {
        if ((int) $version->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }
    }

    private function assertTransition(RotaVersion $version, RotaVersionStatus $target): void
    {
        if (! $version->status->canTransitionTo($target)) {
            throw ValidationException::withMessages([
                'rota' => 'Cannot move from '.$version->status->label().' to '.$target->label().'.',
            ]);
        }
    }
}
