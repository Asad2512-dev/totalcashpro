<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Wage extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'user_id',
        'hours_worked',
        'amount',
        'notes',
        'status',
        'paid_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'hours_worked' => 'decimal:2',
            'amount' => 'decimal:2',
            'paid_date' => 'date',
            'status' => WageStatus::class,
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
