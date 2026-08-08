<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_drawers', function (Blueprint $table): void {
            if (! Schema::hasColumn('cash_drawers', 'status')) {
                $table->string('status', 20)->default('active')->after('is_active');
            }
            if (! Schema::hasColumn('cash_drawers', 'last_cash_up_at')) {
                $table->timestamp('last_cash_up_at')->nullable()->after('last_closed_at');
            }
            if (! Schema::hasColumn('cash_drawers', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('settings')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('cash_drawers', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('cash_drawers', function (Blueprint $table): void {
            $table->unique(['organization_id', 'branch_id', 'code'], 'cash_drawers_branch_code_unique');
        });

        Schema::table('cash_ups', function (Blueprint $table): void {
            $table->dropUnique('cash_ups_unique_shift');
            $table->unique(
                ['organization_id', 'branch_id', 'cash_drawer_id', 'cashup_date', 'shift'],
                'cash_ups_unique_shift_drawer',
            );
        });
    }

    public function down(): void
    {
        Schema::table('cash_ups', function (Blueprint $table): void {
            $table->dropUnique('cash_ups_unique_shift_drawer');
            $table->unique(['organization_id', 'branch_id', 'cashup_date', 'shift'], 'cash_ups_unique_shift');
        });

        Schema::table('cash_drawers', function (Blueprint $table): void {
            $table->dropUnique('cash_drawers_branch_code_unique');
            foreach (['status', 'last_cash_up_at', 'created_by', 'updated_by'] as $col) {
                if (Schema::hasColumn('cash_drawers', $col)) {
                    if (in_array($col, ['created_by', 'updated_by'], true)) {
                        $table->dropConstrainedForeignId($col);
                    } else {
                        $table->dropColumn($col);
                    }
                }
            }
        });
    }
};
