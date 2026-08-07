<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\FinanceAttachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasFinanceAttachments
{
    public function financeAttachments(): MorphMany
    {
        return $this->morphMany(FinanceAttachment::class, 'attachable');
    }
}
