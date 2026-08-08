<?php

declare(strict_types=1);

namespace App\Enums;

enum RotaVersionStatus: string
{
    case Draft = 'draft';
    case Finalized = 'finalized';
    case Published = 'published';
    case Locked = 'locked';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Finalized => 'Finalized',
            self::Published => 'Published',
            self::Locked => 'Locked',
            self::Archived => 'Archived',
        };
    }

    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    public function isVisibleToStaff(): bool
    {
        return in_array($this, [self::Published, self::Locked], true);
    }

    /**
     * @return list<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::Finalized],
            self::Finalized => [self::Published, self::Draft],
            self::Published => [self::Locked],
            self::Locked => [self::Archived, self::Draft],
            self::Archived => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }
}
