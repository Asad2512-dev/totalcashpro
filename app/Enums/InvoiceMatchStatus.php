<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceMatchStatus: string
{
    case Pending = 'pending';
    case Matched = 'matched';
    case Mismatch = 'mismatch';
    case ApprovedException = 'approved_exception';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Matched => 'Matched',
            self::Mismatch => 'Mismatch',
            self::ApprovedException => 'Approved exception',
            self::Rejected => 'Rejected',
        };
    }
}
