<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FinanceIncomeSource;
use App\Enums\FinanceStatus;
use App\Models\Concerns\HasFinanceAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class FinanceIncomeEntry extends Model
{
    use HasFinanceAttachments;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'bank_account_id',
        'source',
        'reference_type',
        'reference_id',
        'title',
        'net_amount',
        'vat_amount',
        'gross_amount',
        'income_date',
        'status',
        'notes',
        'approved_at',
        'paid_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'source' => FinanceIncomeSource::class,
            'net_amount' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'income_date' => 'date',
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

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceBankAccount::class, 'bank_account_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
