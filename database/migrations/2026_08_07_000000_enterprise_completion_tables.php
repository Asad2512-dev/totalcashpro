<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table): void {
            $table->foreignId('manager_user_id')->nullable()->after('organization_id')->constrained('users')->nullOnDelete();
            $table->string('phone', 40)->nullable()->after('address');
            $table->string('email')->nullable()->after('phone');
            $table->string('postcode', 20)->nullable()->after('email');
            $table->json('opening_hours')->nullable()->after('postcode');
            $table->text('receipt_footer')->nullable()->after('opening_hours');
            $table->foreignId('finance_bank_account_id')->nullable()->after('receipt_footer')->constrained('finance_bank_accounts')->nullOnDelete();
            $table->json('settings')->nullable()->after('finance_bank_account_id');
        });

        Schema::table('inventory_items', function (Blueprint $table): void {
            $table->string('sku', 80)->nullable()->after('name');
            $table->string('barcode', 80)->nullable()->after('sku');
            $table->string('brand', 120)->nullable()->after('barcode');
            $table->decimal('cost_price', 12, 2)->nullable()->after('brand');
            $table->decimal('selling_price', 12, 2)->nullable()->after('cost_price');
            $table->foreignId('supplier_id')->nullable()->after('selling_price')->constrained('suppliers')->nullOnDelete();
            $table->string('batch_number', 80)->nullable()->after('supplier_id');
            $table->date('expiry_date')->nullable()->after('batch_number');
            $table->index(['organization_id', 'sku']);
            $table->index(['organization_id', 'barcode']);
        });

        Schema::table('notifications', function (Blueprint $table): void {
            $table->string('category', 40)->default('system')->after('type');
            $table->index(['user_id', 'category']);
        });

        Schema::create('cash_drawers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->foreignId('finance_bank_account_id')->nullable()->constrained('finance_bank_accounts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['organization_id', 'branch_id']);
        });

        Schema::table('branches', function (Blueprint $table): void {
            $table->foreignId('cash_drawer_id')->nullable()->after('finance_bank_account_id')->constrained('cash_drawers')->nullOnDelete();
        });

        Schema::create('petty_cash_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('float_amount', 12, 2)->default(0);
            $table->foreignId('custodian_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('petty_cash_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('petty_cash_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->date('transaction_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['organization_id', 'transaction_date']);
        });

        Schema::create('recurring_bills', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('vendor')->nullable();
            $table->string('category')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('frequency', 20);
            $table->date('next_due_date');
            $table->date('last_generated_date')->nullable();
            $table->foreignId('finance_bank_account_id')->nullable()->constrained('finance_bank_accounts')->nullOnDelete();
            $table->string('status', 20)->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['organization_id', 'next_due_date']);
        });

        Schema::create('scheduled_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payable_type');
            $table->unsignedBigInteger('payable_id');
            $table->decimal('amount', 12, 2);
            $table->date('scheduled_date');
            $table->string('status', 20)->default('pending');
            $table->foreignId('finance_bank_account_id')->nullable()->constrained('finance_bank_accounts')->nullOnDelete();
            $table->timestamps();
            $table->index(['organization_id', 'scheduled_date', 'status']);
        });

        Schema::create('purchase_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('po_number', 40);
            $table->string('status', 20)->default('draft');
            $table->date('ordered_at')->nullable();
            $table->date('expected_at')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('vat_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['organization_id', 'po_number']);
        });

        Schema::create('purchase_order_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->string('description');
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_cost', 12, 2);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });

        Schema::create('goods_received_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('received_at');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_transfers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('to_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity_pcs');
            $table->string('status', 20)->default('pending');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('stock_adjustments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->integer('adjustment_pcs');
            $table->string('reason');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('inventory_waste', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity_pcs');
            $table->string('reason')->nullable();
            $table->decimal('cost_impact', 12, 2)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('staff_availability', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'day_of_week']);
        });

        Schema::create('leave_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 20)->default('pending');
            $table->text('reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'status']);
        });

        Schema::create('shift_swap_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('target_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rota_shift_id')->constrained('rota_shifts')->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->text('reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_emergency_contacts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('relationship', 60)->nullable();
            $table->string('phone', 40);
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_employee_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('document_type', 40);
            $table->string('file_path');
            $table->date('expires_at')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('hr_training_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->date('completed_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('provider')->nullable();
            $table->timestamps();
        });

        Schema::create('hr_warnings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('level', 20);
            $table->text('summary');
            $table->date('issued_at');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('hr_contracts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('contract_type', 40);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_customers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->json('marketing_preferences')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'email']);
        });

        Schema::create('crm_customer_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('crm_customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('crm_customer_visits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('crm_customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('visited_at');
            $table->decimal('spend_amount', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('saved_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('report_type', 40);
            $table->json('filters');
            $table->timestamps();
        });

        Schema::create('scheduled_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('saved_report_id')->nullable()->constrained()->nullOnDelete();
            $table->string('frequency', 20);
            $table->time('run_at')->nullable();
            $table->json('recipients')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_reports');
        Schema::dropIfExists('saved_reports');
        Schema::dropIfExists('crm_customer_visits');
        Schema::dropIfExists('crm_customer_notes');
        Schema::dropIfExists('crm_customers');
        Schema::dropIfExists('hr_contracts');
        Schema::dropIfExists('hr_warnings');
        Schema::dropIfExists('hr_training_records');
        Schema::dropIfExists('hr_employee_documents');
        Schema::dropIfExists('hr_emergency_contacts');
        Schema::dropIfExists('shift_swap_requests');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('staff_availability');
        Schema::dropIfExists('inventory_waste');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('goods_received_notes');
        Schema::dropIfExists('purchase_order_lines');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('scheduled_payments');
        Schema::dropIfExists('recurring_bills');
        Schema::dropIfExists('petty_cash_transactions');
        Schema::dropIfExists('petty_cash_accounts');

        Schema::table('branches', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('cash_drawer_id');
        });

        Schema::dropIfExists('cash_drawers');

        Schema::table('notifications', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'category']);
            $table->dropColumn('category');
        });

        Schema::table('inventory_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropColumn(['sku', 'barcode', 'brand', 'cost_price', 'selling_price', 'batch_number', 'expiry_date']);
        });

        Schema::table('branches', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('manager_user_id');
            $table->dropConstrainedForeignId('finance_bank_account_id');
            $table->dropColumn(['phone', 'email', 'postcode', 'opening_hours', 'receipt_footer', 'settings']);
        });
    }
};
