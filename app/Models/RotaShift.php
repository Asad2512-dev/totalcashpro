<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RotaShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'rota_version_id',
        'organization_id',
        'branch_id',
        'user_id',
        'rota_section_id',
        'rota_group_id',
        'shift_date',
        'start_time',
        'end_time',
        'shift_type',
        'break_minutes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'shift_date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function rotaVersion(): BelongsTo
    {
        return $this->belongsTo(RotaVersion::class);
    }

    public function version(): BelongsTo
    {
        return $this->rotaVersion();
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

    public function rotaSection(): BelongsTo
    {
        return $this->belongsTo(RotaSection::class, 'rota_section_id');
    }

    public function section(): BelongsTo
    {
        return $this->rotaSection();
    }

    public function rotaGroup(): BelongsTo
    {
        return $this->belongsTo(RotaGroup::class, 'rota_group_id');
    }

    public function group(): BelongsTo
    {
        return $this->rotaGroup();
    }
}
