<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('remember_token');
            }
            if (! Schema::hasColumn('users', 'two_factor_method')) {
                $table->string('two_factor_method', 20)->nullable()->after('two_factor_enabled');
            }
            if (! Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_method');
            }
            if (! Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('two_factor_confirmed_at');
            }
        });

        Schema::create('otp_codes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('purpose', 50);
            $table->string('code_hash');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'purpose']);
            $table->index('expires_at');
        });

        Schema::create('two_factor_recovery_codes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code_hash');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'used_at']);
        });

        Schema::create('login_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('role', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('browser', 120)->nullable();
            $table->string('device', 120)->nullable();
            $table->string('operating_system', 120)->nullable();
            $table->string('country', 80)->nullable();
            $table->boolean('success')->default(false);
            $table->string('failure_reason')->nullable();
            $table->timestamp('logged_in_at');
            $table->timestamps();

            $table->index(['user_id', 'logged_in_at']);
        });

        Schema::create('user_devices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('device_name')->nullable();
            $table->string('browser', 120)->nullable();
            $table->string('operating_system', 120)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->boolean('is_current')->default(false);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('logged_out_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'logged_out_at']);
        });

        Schema::create('security_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event', 80);
            $table->string('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('event');
        });

        Schema::create('billing_webhook_events', function (Blueprint $table): void {
            $table->id();
            $table->string('provider', 30)->default('stripe');
            $table->string('external_id')->unique();
            $table->string('type', 120);
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['provider', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_webhook_events');
        Schema::dropIfExists('security_logs');
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('two_factor_recovery_codes');
        Schema::dropIfExists('otp_codes');

        Schema::table('users', function (Blueprint $table): void {
            $columns = ['two_factor_enabled', 'two_factor_method', 'two_factor_confirmed_at', 'password_changed_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
