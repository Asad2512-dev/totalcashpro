<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FinanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class FinancePayrollRun extends Model
{
    protected $fillable = [
        'organization_id',
        'branch_id',
        'week_start',
        'week_end',
        'payment_due_date',
        'status',
        'notes',
        'approved_at',
        'paid_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
            'payment_due_date' => 'date',
            'status' => FinanceStatus::class,
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function wages(): HasMany
    {
        return $this->hasMany(Wage::class, 'payroll_run_id');
    }
}
