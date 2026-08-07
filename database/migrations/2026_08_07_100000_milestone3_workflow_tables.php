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
        Schema::table('purchase_orders', function (Blueprint $table): void {
            $table->timestamp('approved_at')->nullable()->after('notes');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->foreignId('supplier_invoice_id')->nullable()->after('approved_by')->constrained('supplier_invoices')->nullOnDelete();
        });

        Schema::table('purchase_order_lines', function (Blueprint $table): void {
            $table->decimal('quantity_received', 12, 3)->default(0)->after('quantity');
            $table->decimal('vat_rate', 5, 2)->default(20)->after('unit_cost');
        });

        Schema::create('goods_received_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('goods_received_note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_line_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_received', 12, 3)->default(0);
            $table->decimal('quantity_damaged', 12, 3)->default(0);
            $table->decimal('quantity_missing', 12, 3)->default(0);
            $table->timestamps();
        });

        Schema::table('supplier_invoices', function (Blueprint $table): void {
            $table->foreignId('purchase_order_id')->nullable()->after('supplier_id')->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('goods_received_note_id')->nullable()->after('purchase_order_id')->constrained('goods_received_notes')->nullOnDelete();
            $table->decimal('amount_paid', 12, 2)->default(0)->after('gross_amount');
        });

        Schema::table('bills', function (Blueprint $table): void {
            $table->foreignId('supplier_invoice_id')->nullable()->after('branch_id')->constrained('supplier_invoices')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->after('supplier_invoice_id')->constrained('purchase_orders')->nullOnDelete();
        });

        Schema::table('finance_supplier_payments', function (Blueprint $table): void {
            $table->string('payment_method', 30)->default('bank_transfer')->after('reference');
        });

        Schema::table('petty_cash_accounts', function (Blueprint $table): void {
            if (! Schema::hasColumn('petty_cash_accounts', 'opening_balance')) {
                $table->decimal('opening_balance', 12, 2)->default(0)->after('float_amount');
            }
        });

        Schema::table('cash_drawers', function (Blueprint $table): void {
            if (! Schema::hasColumn('cash_drawers', 'notes')) {
                $table->text('notes')->nullable()->after('is_active');
            }
        });

        Schema::create('cash_drawer_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cash_drawer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->foreignId('cash_up_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['cash_drawer_id', 'created_at']);
        });

        if (Schema::hasTable('supplier_invoices')) {
            DB::table('supplier_invoices')->where('status', 'Pending')->update(['status' => 'pending']);
            DB::table('supplier_invoices')->where('status', 'Paid')->update(['status' => 'paid']);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_drawer_transactions');

        Schema::table('cash_drawers', function (Blueprint $table): void {
            if (Schema::hasColumn('cash_drawers', 'notes')) {
                $table->dropColumn('notes');
            }
        });

        Schema::table('finance_supplier_payments', function (Blueprint $table): void {
            $table->dropColumn('payment_method');
        });

        Schema::table('bills', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('purchase_order_id');
            $table->dropConstrainedForeignId('supplier_invoice_id');
        });

        Schema::table('supplier_invoices', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('goods_received_note_id');
            $table->dropConstrainedForeignId('purchase_order_id');
            $table->dropColumn('amount_paid');
        });

        Schema::dropIfExists('goods_received_lines');

        Schema::table('purchase_order_lines', function (Blueprint $table): void {
            $table->dropColumn(['quantity_received', 'vat_rate']);
        });

        Schema::table('purchase_orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('supplier_invoice_id');
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn('approved_at');
        });
    }
};
