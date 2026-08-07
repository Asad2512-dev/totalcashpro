<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class LeaveRequestRejected
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public LeaveRequest $leaveRequest,
        public User $reviewer,
        public ?string $adminNotes = null,
    ) {}
}
