<?php

declare(strict_types=1);

namespace App\Http\Requests\BusinessAdmin;

use App\Http\Requests\BaseFormRequest;

abstract class BusinessAdminFormRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true
            && $this->user()->organization_id !== null;
    }

    protected function organizationId(): int
    {
        return (int) $this->user()->organization_id;
    }
}
