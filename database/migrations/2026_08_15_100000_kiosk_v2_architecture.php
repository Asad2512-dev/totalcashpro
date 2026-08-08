<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('organization_kiosk_settings')) {
            Schema::create('organization_kiosk_settings', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->unique()->constrained()->cascadeOnDelete();
                $table->foreignId('default_branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->string('display_name')->nullable();
                $table->boolean('show_attendance_list')->default(true);
                $table->boolean('show_staff_names')->default(true);
                $table->unsignedSmallInteger('success_delay_seconds')->default(3);
                $table->unsignedSmallInteger('session_lifetime_minutes')->default(480);
                $table->json('settings')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('kiosk_break_types')) {
            Schema::create('kiosk_break_types', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('slug', 50);
                $table->text('description')->nullable();
                $table->boolean('is_paid')->default(false);
                $table->unsignedSmallInteger('max_duration_minutes')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedSmallInteger('display_order')->default(0);
                $table->timestamps();
                $table->unique(['organization_id', 'slug']);
            });
        }

        Schema::table('kiosk_sessions', function (Blueprint $table): void {
            if (! Schema::hasColumn('kiosk_sessions', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }
            if (! Schema::hasColumn('kiosk_sessions', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('kiosk_sessions', 'status')) {
                $table->string('status', 20)->default('active')->after('session_token');
            }
            if (! Schema::hasColumn('kiosk_sessions', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable()->after('started_at');
            }
            if (! Schema::hasColumn('kiosk_sessions', 'revoked_at')) {
                $table->timestamp('revoked_at')->nullable()->after('ended_at');
            }
            if (! Schema::hasColumn('kiosk_sessions', 'revoked_by_user_id')) {
                $table->foreignId('revoked_by_user_id')->nullable()->after('revoked_at')->constrained('users')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('kiosk_sessions', 'branch_kiosk_id')) {
            Schema::table('kiosk_sessions', function (Blueprint $table): void {
                $table->foreignId('branch_kiosk_id')->nullable()->change();
            });
        }

        if (Schema::hasTable('kiosk_activity_logs') && Schema::hasColumn('kiosk_activity_logs', 'branch_kiosk_id')) {
            Schema::table('kiosk_activity_logs', function (Blueprint $table): void {
                $table->foreignId('branch_kiosk_id')->nullable()->change();
            });
        }

        Schema::table('attendance_breaks', function (Blueprint $table): void {
            if (! Schema::hasColumn('attendance_breaks', 'kiosk_break_type_id')) {
                $table->foreignId('kiosk_break_type_id')->nullable()->after('break_type')->constrained('kiosk_break_types')->nullOnDelete();
            }
            if (! Schema::hasColumn('attendance_breaks', 'status')) {
                $table->string('status', 20)->default('active')->after('break_ended_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_breaks', function (Blueprint $table): void {
            foreach (['kiosk_break_type_id', 'status'] as $col) {
                if (Schema::hasColumn('attendance_breaks', $col)) {
                    if ($col === 'kiosk_break_type_id') {
                        $table->dropConstrainedForeignId($col);
                    } else {
                        $table->dropColumn($col);
                    }
                }
            }
        });

        Schema::table('kiosk_sessions', function (Blueprint $table): void {
            foreach (['organization_id', 'branch_id', 'status', 'last_activity_at', 'revoked_at', 'revoked_by_user_id'] as $col) {
                if (Schema::hasColumn('kiosk_sessions', $col)) {
                    if (in_array($col, ['organization_id', 'branch_id', 'revoked_by_user_id'], true)) {
                        $table->dropConstrainedForeignId($col);
                    } else {
                        $table->dropColumn($col);
                    }
                }
            }
        });

        Schema::dropIfExists('kiosk_break_types');
        Schema::dropIfExists('organization_kiosk_settings');
    }
};
