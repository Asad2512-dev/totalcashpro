<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rota_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->unsignedSmallInteger('version_number')->default(1);
            $table->string('status', 20)->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('finalized_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'branch_id', 'week_start', 'version_number'], 'rota_versions_unique');
            $table->index(['organization_id', 'branch_id', 'week_start', 'status'], 'rota_versions_week_status_idx');
        });

        Schema::create('rota_amendments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rota_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('rota_shift_id')->nullable()->constrained('rota_shifts')->nullOnDelete();
            $table->string('field', 60);
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->text('reason');
            $table->foreignId('amended_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['rota_version_id', 'created_at']);
        });

        Schema::table('rota_shifts', function (Blueprint $table): void {
            $table->foreignId('rota_version_id')->nullable()->after('id')->constrained('rota_versions')->cascadeOnDelete();
            $table->unsignedSmallInteger('break_minutes')->default(0)->after('shift_type');
            $table->index(['rota_version_id', 'shift_date'], 'rota_shifts_version_date_idx');
        });

        Schema::table('attendance_logs', function (Blueprint $table): void {
            $table->foreignId('rota_shift_id')->nullable()->after('branch_kiosk_id')->constrained('rota_shifts')->nullOnDelete();
        });

        $this->migrateExistingShifts();
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('rota_shift_id');
        });

        Schema::table('rota_shifts', function (Blueprint $table): void {
            $table->dropIndex('rota_shifts_version_date_idx');
            $table->dropConstrainedForeignId('rota_version_id');
            $table->dropColumn('break_minutes');
        });

        Schema::dropIfExists('rota_amendments');
        Schema::dropIfExists('rota_versions');
    }

    private function migrateExistingShifts(): void
    {
        if (! Schema::hasTable('rota_shifts')) {
            return;
        }

        $groups = DB::table('rota_shifts')
            ->select('organization_id', 'branch_id', 'shift_date')
            ->distinct()
            ->get();

        $versionMap = [];

        foreach ($groups as $row) {
            $weekStart = \Illuminate\Support\Carbon::parse($row->shift_date)->startOfWeek()->toDateString();
            $key = "{$row->organization_id}:{$row->branch_id}:{$weekStart}";

            if (! isset($versionMap[$key])) {
                $status = 'published';
                $versionId = DB::table('rota_versions')->insertGetId([
                    'organization_id' => $row->organization_id,
                    'branch_id' => $row->branch_id,
                    'week_start' => $weekStart,
                    'version_number' => 1,
                    'status' => $status,
                    'published_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $versionMap[$key] = $versionId;
            }
        }

        foreach ($versionMap as $key => $versionId) {
            [$orgId, $branchId, $weekStart] = explode(':', $key);
            $weekEnd = \Illuminate\Support\Carbon::parse($weekStart)->endOfWeek()->toDateString();

            DB::table('rota_shifts')
                ->where('organization_id', $orgId)
                ->where('branch_id', $branchId)
                ->whereBetween('shift_date', [$weekStart, $weekEnd])
                ->whereNull('rota_version_id')
                ->update(['rota_version_id' => $versionId]);
        }
    }
};
