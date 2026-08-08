<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OrganizationKioskSetting extends Model
{
    protected $fillable = [
        'organization_id',
        'default_branch_id',
        'display_name',
        'show_attendance_list',
        'show_staff_names',
        'success_delay_seconds',
        'session_lifetime_minutes',
        'settings',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'show_attendance_list' => 'boolean',
            'show_staff_names' => 'boolean',
            'settings' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function defaultBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'default_branch_id');
    }
}
