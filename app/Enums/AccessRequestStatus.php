<?php

declare(strict_types=1);

namespace App\Enums;

enum AccessRequestStatus: string
{
    case Pending = 'pending';
    case Reviewed = 'reviewed';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
