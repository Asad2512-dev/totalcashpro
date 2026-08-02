<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CashUpShift;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CashUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'branch_id',
        'cashup_date',
        'shift',
        'coins_total',
        'coins_detail',
        'notes_total',
        'notes_detail',
        'cards_total',
        'cards_detail',
        'expenses_total',
        'expenses_detail',
        'online_orders_total',
        'online_orders_detail',
        'platform_deductions_total',
        'platform_deductions_detail',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'cashup_date' => 'date',
            'shift' => CashUpShift::class,
            'coins_total' => 'decimal:2',
            'coins_detail' => 'array',
            'notes_total' => 'decimal:2',
            'notes_detail' => 'array',
            'cards_total' => 'decimal:2',
            'cards_detail' => 'array',
            'expenses_total' => 'decimal:2',
            'expenses_detail' => 'array',
            'online_orders_total' => 'decimal:2',
            'online_orders_detail' => 'array',
            'platform_deductions_total' => 'decimal:2',
            'platform_deductions_detail' => 'array',
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

    public function grossTotal(): float
    {
        return (float) $this->coins_total
            + (float) $this->notes_total
            + (float) $this->cards_total
            + (float) $this->online_orders_total;
    }

    public function netTotal(): float
    {
        return $this->grossTotal()
            - (float) $this->expenses_total
            - (float) $this->platform_deductions_total;
    }
}

