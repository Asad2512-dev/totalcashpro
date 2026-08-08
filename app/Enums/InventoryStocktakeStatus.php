<?php

declare(strict_types=1);

namespace App\Enums;

enum InventoryStocktakeStatus: string
{
    case Draft = 'draft';
    case InProgress = 'in_progress';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Ordered = 'ordered';
    case Completed = 'completed';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::InProgress => 'In progress',
            self::Submitted => 'Submitted',
            self::UnderReview => 'Under review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Ordered => 'Ordered',
            self::Completed => 'Completed',
            self::Archived => 'Archived',
        };
    }

    public function isEditableByStaff(): bool
    {
        return in_array($this, [self::Draft, self::InProgress, self::Rejected], true);
    }
}
