<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\FinanceAttachment;
use App\Repositories\Contracts\FinanceAttachmentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

final class FinanceAttachmentRepository extends BaseRepository implements FinanceAttachmentRepositoryInterface
{
    public function __construct(FinanceAttachment $model)
    {
        parent::__construct($model);
    }

    public function attachTo(Model $model, array $data): FinanceAttachment
    {
        return $this->model->newQuery()->create(array_merge($data, [
            'attachable_type' => $model->getMorphClass(),
            'attachable_id' => $model->getKey(),
        ]));
    }
}
