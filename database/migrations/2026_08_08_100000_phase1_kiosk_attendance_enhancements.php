<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branch_kiosks', function (Blueprint $table): void {
            $table->text('description')->nullable()->after('name');
            $table->json('settings')->nullable()->after('show_photos');
        });

        Schema::table('attendance_logs', function (Blueprint $table): void {
            $table->foreignId('branch_kiosk_id')->nullable()->after('user_id')->constrained('branch_kiosks')->nullOnDelete();
            $table->string('source', 30)->default('manual')->after('type');
            $table->string('idempotency_key', 64)->nullable()->unique()->after('logged_at');
        });

        Schema::table('attendance_breaks', function (Blueprint $table): void {
            $table->string('break_type', 30)->default('other')->after('user_id');
            $table->boolean('is_paid')->default(false)->after('break_ended_at');
            $table->unsignedSmallInteger('planned_minutes')->nullable()->after('is_paid');
            $table->string('source', 30)->default('manual')->after('planned_minutes');
            $table->foreignId('branch_kiosk_id')->nullable()->after('source')->constrained('branch_kiosks')->nullOnDelete();
            $table->text('notes')->nullable()->after('branch_kiosk_id');
        });

        Schema::table('rota_shifts', function (Blueprint $table): void {
            $table->string('status', 20)->default('published')->after('shift_type');
            $table->index(['user_id', 'shift_date', 'status'], 'rota_shifts_user_date_status_idx');
        });

        Schema::create('kiosk_sync_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_kiosk_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 30);
            $table->string('idempotency_key', 64);
            $table->unsignedBigInteger('client_sequence')->nullable();
            $table->dateTime('event_time');
            $table->string('sync_status', 20)->default('synced');
            $table->json('payload')->nullable();
            $table->json('result')->nullable();
            $table->timestamps();

            $table->unique(['branch_kiosk_id', 'idempotency_key'], 'kiosk_sync_events_idempotent');
            $table->index(['organization_id', 'event_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kiosk_sync_events');

        Schema::table('rota_shifts', function (Blueprint $table): void {
            $table->dropIndex('rota_shifts_user_date_status_idx');
            $table->dropColumn('status');
        });

        Schema::table('attendance_breaks', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('branch_kiosk_id');
            $table->dropColumn(['break_type', 'is_paid', 'planned_minutes', 'source', 'notes']);
        });

        Schema::table('attendance_logs', function (Blueprint $table): void {
            $table->dropUnique(['idempotency_key']);
            $table->dropConstrainedForeignId('branch_kiosk_id');
            $table->dropColumn(['source', 'idempotency_key']);
        });

        Schema::table('branch_kiosks', function (Blueprint $table): void {
            $table->dropColumn(['description', 'settings']);
        });
    }
};
