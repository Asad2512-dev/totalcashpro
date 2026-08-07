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
        'payroll_run_id',
        'hours_worked',
        'amount',
        'net_amount',
        'vat_amount',
        'gross_amount',
        'period_start',
        'period_end',
        'payment_due_date',
        'from_attendance',
        'notes',
        'status',
        'approved_at',
        'paid_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'hours_worked' => 'decimal:2',
            'amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'period_start' => 'date',
            'period_end' => 'date',
            'payment_due_date' => 'date',
            'from_attendance' => 'boolean',
            'paid_date' => 'date',
            'status' => WageStatus::class,
            'approved_at' => 'datetime',
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

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(FinancePayrollRun::class, 'payroll_run_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
