<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Branch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'organization_id', 'manager_user_id', 'name', 'slug', 'city', 'address', 'phone', 'email',
        'postcode', 'opening_hours', 'receipt_footer', 'finance_bank_account_id', 'cash_drawer_id',
        'settings', 'status', 'staff_count',
    ];

    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
            'settings' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(FinanceBankAccount::class, 'finance_bank_account_id');
    }

    public function cashDrawer(): BelongsTo
    {
        return $this->belongsTo(CashDrawer::class, 'cash_drawer_id');
    }

    public function kiosks(): HasMany
    {
        return $this->hasMany(BranchKiosk::class);
    }
}
