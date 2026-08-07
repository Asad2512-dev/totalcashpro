<?php

declare(strict_types=1);

namespace App\Services\BusinessAdmin;

use App\Contracts\ServiceInterface;
use App\Enums\RequestStatus;
use App\Events\LeaveRequestApproved;
use App\Events\LeaveRequestRejected;
use App\Events\ShiftSwapApproved;
use App\Events\ShiftSwapRejected;
use App\Models\LeaveRequest;
use App\Models\ShiftSwapRequest;
use App\Models\User;
use Illuminate\Support\Collection;

final class HrAdminService implements ServiceInterface
{
    public function __construct(private readonly BranchContext $branchContext) {}

    /**
     * @return Collection<int, LeaveRequest>
     */
    public function pendingLeave(User $admin): Collection
    {
        $branchId = $this->branchContext->currentBranchId($admin);

        return LeaveRequest::query()
            ->with('user')
            ->where('organization_id', $admin->organization_id)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', RequestStatus::Pending)
            ->latest()
            ->limit(100)
            ->get();
    }

    /**
     * @return Collection<int, ShiftSwapRequest>
     */
    public function pendingShiftSwaps(User $admin): Collection
    {
        $branchId = $this->branchContext->currentBranchId($admin);

        return ShiftSwapRequest::query()
            ->with(['requester', 'rotaShift'])
            ->where('organization_id', $admin->organization_id)
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', RequestStatus::Pending)
            ->latest()
            ->get();
    }

    public function reviewLeave(User $admin, LeaveRequest $request, string $action, ?string $notes = null): LeaveRequest
    {
        $this->authorizeLeave($admin, $request);

        $status = $action === 'approve' ? RequestStatus::Approved : RequestStatus::Rejected;
        $request->update([
            'status' => $status,
            'admin_notes' => $notes,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        if ($status === RequestStatus::Approved) {
            LeaveRequestApproved::dispatch($request->fresh(), $admin);
        } else {
            LeaveRequestRejected::dispatch($request->fresh(), $admin, $notes);
        }

        return $request->refresh();
    }

    public function reviewShiftSwap(User $admin, ShiftSwapRequest $swap, string $action, ?string $notes = null): ShiftSwapRequest
    {
        $this->authorizeSwap($admin, $swap);

        if ($action === 'approve') {
            ShiftSwapApproved::dispatch($swap, $admin);
        } else {
            $swap->update([
                'status' => RequestStatus::Rejected,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);
            ShiftSwapRejected::dispatch($swap->fresh(), $admin, $notes);
        }

        return $swap->refresh();
    }

    private function authorizeLeave(User $admin, LeaveRequest $request): void
    {
        if ((int) $request->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }
    }

    private function authorizeSwap(User $admin, ShiftSwapRequest $swap): void
    {
        if ((int) $swap->organization_id !== (int) $admin->organization_id) {
            abort(403);
        }
    }
}
