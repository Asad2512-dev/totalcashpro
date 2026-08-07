<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CrmCustomer extends Model
{
    protected $fillable = [
        'organization_id', 'branch_id', 'name', 'email', 'phone', 'marketing_preferences', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'marketing_preferences' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
