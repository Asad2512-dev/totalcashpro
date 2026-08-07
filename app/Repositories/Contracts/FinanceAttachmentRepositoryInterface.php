<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\FinanceAttachment;
use Illuminate\Database\Eloquent\Model;

interface FinanceAttachmentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function attachTo(Model $model, array $data): FinanceAttachment;
}
