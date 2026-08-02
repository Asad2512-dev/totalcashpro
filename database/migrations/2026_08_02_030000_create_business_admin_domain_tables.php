<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'pin_code')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('pin_code', 4)->nullable()->after('phone');
                $table->decimal('hourly_rate', 10, 2)->nullable()->after('pin_code');
                $table->string('address')->nullable()->after('hourly_rate');
                $table->text('notes')->nullable()->after('address');
                $table->unique(['organization_id', 'pin_code'], 'users_org_pin_unique');
            });
        }

        if (! Schema::hasColumn('organizations', 'logo_path')) {
            Schema::table('organizations', function (Blueprint $table): void {
                $table->string('logo_path')->nullable()->after('tax_number');
                $table->string('opens_at', 20)->nullable()->after('logo_path');
                $table->string('closes_at', 20)->nullable()->after('opens_at');
                $table->json('settings')->nullable()->after('closes_at');
            });
        }

        if (Schema::hasTable('cash_ups')) {
            return;
        }

        Schema::create('cash_ups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('cashup_date');
            $table->string('shift', 20);
            $table->decimal('coins_total', 12, 2)->default(0);
            $table->json('coins_detail')->nullable();
            $table->decimal('notes_total', 12, 2)->default(0);
            $table->json('notes_detail')->nullable();
            $table->decimal('cards_total', 12, 2)->default(0);
            $table->json('cards_detail')->nullable();
            $table->decimal('expenses_total', 12, 2)->default(0);
            $table->json('expenses_detail')->nullable();
            $table->decimal('online_orders_total', 12, 2)->default(0);
            $table->json('online_orders_detail')->nullable();
            $table->decimal('platform_deductions_total', 12, 2)->default(0);
            $table->json('platform_deductions_detail')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['organization_id', 'branch_id', 'cashup_date', 'shift'], 'cash_ups_unique_shift');
            $table->index(['organization_id', 'cashup_date']);
        });

        Schema::create('attendance_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->dateTime('logged_at');
            $table->timestamps();

            $table->index(['organization_id', 'user_id', 'logged_at'], 'attendance_logs_org_user_time_idx');
        });

        Schema::create('attendance_breaks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->dateTime('break_started_at');
            $table->dateTime('break_ended_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'break_started_at'], 'attendance_breaks_user_time_idx');
        });

        Schema::create('inventory_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inventory_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('inventory_categories')->nullOnDelete();
            $table->string('name');
            $table->string('packaging', 20)->default('pcs');
            $table->unsignedInteger('pcs_per_box')->default(1);
            $table->integer('stock_total_pcs')->default(0);
            $table->integer('stock_limit')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'branch_id']);
        });

        Schema::create('inventory_counts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->integer('diff_pcs')->default(0);
            $table->integer('new_pcs')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('supplier_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_no');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('Pending');
            $table->date('paid_date')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });

        Schema::create('wages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('hours_worked', 8, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('Pending');
            $table->dateTime('paid_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('rota_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 20)->default('#16A34A');
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('rota_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 20)->default('#0F766E');
            $table->timestamps();
        });

        Schema::create('rota_shifts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rota_section_id')->constrained('rota_sections')->cascadeOnDelete();
            $table->foreignId('rota_group_id')->nullable()->constrained('rota_groups')->nullOnDelete();
            $table->date('shift_date');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('shift_type', 20);
            $table->timestamps();

            $table->index(['organization_id', 'shift_date'], 'rota_shifts_org_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rota_shifts');
        Schema::dropIfExists('rota_sections');
        Schema::dropIfExists('rota_groups');
        Schema::dropIfExists('wages');
        Schema::dropIfExists('supplier_invoices');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('inventory_counts');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_categories');
        Schema::dropIfExists('attendance_breaks');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('cash_ups');

        Schema::table('organizations', function (Blueprint $table): void {
            $table->dropColumn(['logo_path', 'opens_at', 'closes_at', 'settings']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique('users_org_pin_unique');
            $table->dropColumn(['pin_code', 'hourly_rate', 'address', 'notes']);
        });
    }
};
