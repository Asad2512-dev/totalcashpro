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
            $table->string('code', 30)->nullable()->after('name');
            $table->string('currency', 3)->default('GBP')->after('current_balance');
            $table->foreignId('assigned_user_id')->nullable()->after('currency')->constrained('users')->nullOnDelete();
            $table->timestamp('last_opened_at')->nullable()->after('is_active');
            $table->timestamp('last_closed_at')->nullable()->after('last_opened_at');
            $table->json('settings')->nullable()->after('last_closed_at');
        });

        Schema::create('cash_drawer_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cash_drawer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opened_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_float', 12, 2)->default(0);
            $table->json('opening_count')->nullable();
            $table->json('closing_count')->nullable();
            $table->decimal('expected_cash', 12, 2)->nullable();
            $table->decimal('actual_cash', 12, 2)->nullable();
            $table->decimal('variance', 12, 2)->nullable();
            $table->string('variance_reason')->nullable();
            $table->string('status', 20)->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['cash_drawer_id', 'status']);
            $table->index(['organization_id', 'branch_id', 'opened_at']);
        });

        Schema::table('cash_drawer_transactions', function (Blueprint $table): void {
            $table->foreignId('cash_drawer_session_id')->nullable()->after('cash_drawer_id')->constrained()->nullOnDelete();
            $table->string('reference_type')->nullable()->after('description');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            $table->string('reason')->nullable()->after('reference_id');
            $table->foreignId('paired_transaction_id')->nullable()->after('reason')->constrained('cash_drawer_transactions')->nullOnDelete();
            $table->foreignId('transfer_drawer_id')->nullable()->after('paired_transaction_id')->constrained('cash_drawers')->nullOnDelete();
            $table->string('approval_status', 20)->default('approved')->after('transfer_drawer_id');
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::table('cash_ups', function (Blueprint $table): void {
            $table->foreignId('cash_drawer_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->foreignId('cash_drawer_session_id')->nullable()->after('cash_drawer_id')->constrained()->nullOnDelete();
            $table->decimal('opening_float', 12, 2)->default(0)->after('shift');
            $table->json('opening_float_count')->nullable()->after('opening_float');
            $table->decimal('cash_sales_total', 12, 2)->default(0)->after('opening_float_count');
            $table->decimal('expected_cash', 12, 2)->nullable()->after('platform_deductions_detail');
            $table->decimal('actual_cash', 12, 2)->nullable()->after('expected_cash');
            $table->decimal('variance', 12, 2)->nullable()->after('actual_cash');
            $table->string('variance_reason')->nullable()->after('variance');
            $table->string('status', 20)->default('draft')->after('variance_reason');
            $table->foreignId('approved_by_user_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by_user_id');
            $table->timestamp('locked_at')->nullable()->after('approved_at');
            $table->index(['cash_drawer_id', 'cashup_date']);
            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('cash_ups', function (Blueprint $table): void {
            $table->dropIndex(['cash_drawer_id', 'cashup_date']);
            $table->dropIndex(['organization_id', 'status']);
            $table->dropConstrainedForeignId('approved_by_user_id');
            $table->dropConstrainedForeignId('cash_drawer_session_id');
            $table->dropConstrainedForeignId('cash_drawer_id');
            $table->dropColumn([
                'opening_float', 'opening_float_count', 'cash_sales_total',
                'expected_cash', 'actual_cash', 'variance', 'variance_reason',
                'status', 'approved_at', 'locked_at',
            ]);
        });

        Schema::table('cash_drawer_transactions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('transfer_drawer_id');
            $table->dropConstrainedForeignId('paired_transaction_id');
            $table->dropConstrainedForeignId('cash_drawer_session_id');
            $table->dropColumn(['reference_type', 'reference_id', 'reason', 'approval_status']);
        });

        Schema::dropIfExists('cash_drawer_sessions');

        Schema::table('cash_drawers', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('assigned_user_id');
            $table->dropColumn(['code', 'currency', 'last_opened_at', 'last_closed_at', 'settings']);
        });
    }
};
