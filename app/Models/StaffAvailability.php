<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class StaffAvailability extends Model
{
    protected $table = 'staff_availability';

    protected $fillable = [
        'user_id', 'organization_id', 'branch_id', 'day_of_week',
        'start_time', 'end_time', 'is_available',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_available' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
