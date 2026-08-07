<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CrmCustomerVisit extends Model
{
    protected $fillable = [
        'crm_customer_id', 'organization_id', 'branch_id',
        'visited_at', 'spend_amount', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
            'spend_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CrmCustomer::class, 'crm_customer_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
