<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_kiosks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->string('welcome_message')->default('Welcome — enter your PIN to clock in or out.');
            $table->boolean('show_photos')->default(true);
            $table->boolean('is_enabled')->default(true);
            $table->timestamp('last_started_at')->nullable();
            $table->timestamps();

            $table->unique('branch_id');
            $table->index(['organization_id', 'is_enabled']);
        });

        Schema::create('kiosk_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_kiosk_id')->constrained()->cascadeOnDelete();
            $table->string('session_token', 64)->unique();
            $table->foreignId('started_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ended_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_summary')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['branch_kiosk_id', 'ended_at']);
        });

        Schema::create('kiosk_activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_kiosk_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('event', 40);
            $table->foreignId('staff_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_summary')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['branch_kiosk_id', 'created_at']);
            $table->index(['organization_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kiosk_activity_logs');
        Schema::dropIfExists('kiosk_sessions');
        Schema::dropIfExists('branch_kiosks');
    }
};
