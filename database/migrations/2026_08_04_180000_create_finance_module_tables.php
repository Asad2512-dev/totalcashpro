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
        Schema::create('finance_bank_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('bank_name')->nullable();
            $table->string('sort_code', 16)->nullable();
            $table->string('account_number_last4', 4)->nullable();
            $table->string('currency', 3)->default('GBP');
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['organization_id', 'branch_id']);
        });

        Schema::create('finance_payroll_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->date('payment_due_date');
            $table->string('status', 20)->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['organization_id', 'branch_id', 'week_start'], 'finance_payroll_runs_week_unique');
            $table->index(['organization_id', 'status']);
        });

        Schema::create('finance_income_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('finance_bank_accounts')->nullOnDelete();
            $table->string('source', 30)->default('manual');
            $table->nullableMorphs('reference');
            $table->string('title');
            $table->decimal('net_amount', 14, 2)->default(0);
            $table->decimal('vat_amount', 14, 2)->default(0);
            $table->decimal('gross_amount', 14, 2)->default(0);
            $table->date('income_date');
            $table->string('status', 20)->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['organization_id', 'income_date']);
            $table->index(['organization_id', 'status']);
        });

        Schema::create('finance_supplier_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('finance_bank_accounts')->nullOnDelete();
            $table->decimal('net_amount', 14, 2)->default(0);
            $table->decimal('vat_amount', 14, 2)->default(0);
            $table->decimal('gross_amount', 14, 2)->default(0);
            $table->date('payment_date');
            $table->string('reference')->nullable();
            $table->string('status', 20)->default('paid');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['organization_id', 'payment_date']);
        });

        Schema::create('finance_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->morphs('attachable');
            $table->string('disk', 32)->default('public');
            $table->string('path');
            $table->string('filename');
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['organization_id', 'attachable_type', 'attachable_id'], 'finance_attachments_org_morph_idx');
        });

        Schema::create('finance_integration_connections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 40);
            $table->string('status', 20)->default('disconnected');
            $table->string('external_account_id')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'provider'], 'finance_integrations_org_provider_unique');
        });

        Schema::table('spendings', function (Blueprint $table): void {
            $table->decimal('net_amount', 14, 2)->default(0)->after('amount');
            $table->decimal('vat_amount', 14, 2)->default(0)->after('net_amount');
            $table->decimal('gross_amount', 14, 2)->default(0)->after('vat_amount');
            $table->string('status', 20)->default('draft')->after('gross_amount');
            $table->foreignId('bank_account_id')->nullable()->after('status')->constrained('finance_bank_accounts')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('bank_account_id');
            $table->timestamp('paid_at')->nullable()->after('approved_at');
        });

        Schema::table('bills', function (Blueprint $table): void {
            $table->decimal('net_amount', 14, 2)->default(0)->after('amount');
            $table->decimal('vat_amount', 14, 2)->default(0)->after('net_amount');
            $table->decimal('gross_amount', 14, 2)->default(0)->after('vat_amount');
            $table->foreignId('bank_account_id')->nullable()->after('status')->constrained('finance_bank_accounts')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('bank_account_id');
            $table->timestamp('paid_at')->nullable()->after('approved_at');
        });

        Schema::table('supplier_invoices', function (Blueprint $table): void {
            $table->decimal('net_amount', 14, 2)->default(0)->after('amount');
            $table->decimal('vat_amount', 14, 2)->default(0)->after('net_amount');
            $table->decimal('gross_amount', 14, 2)->default(0)->after('vat_amount');
            $table->timestamp('approved_at')->nullable()->after('status');
        });

        Schema::table('wages', function (Blueprint $table): void {
            $table->foreignId('payroll_run_id')->nullable()->after('user_id')->constrained('finance_payroll_runs')->nullOnDelete();
            $table->decimal('net_amount', 14, 2)->default(0)->after('amount');
            $table->decimal('vat_amount', 14, 2)->default(0)->after('net_amount');
            $table->decimal('gross_amount', 14, 2)->default(0)->after('vat_amount');
            $table->date('period_start')->nullable()->after('gross_amount');
            $table->date('period_end')->nullable()->after('period_start');
            $table->date('payment_due_date')->nullable()->after('period_end');
            $table->boolean('from_attendance')->default(false)->after('payment_due_date');
            $table->timestamp('approved_at')->nullable()->after('from_attendance');
        });

        $this->backfillAmounts();
    }

    private function backfillAmounts(): void
    {
        if (Schema::hasTable('bills')) {
            DB::table('bills')->update([
                'gross_amount' => DB::raw('amount'),
                'net_amount' => DB::raw('amount'),
                'vat_amount' => 0,
            ]);
            DB::table('bills')->where('status', 'pending')->update(['status' => 'approved']);
        }

        if (Schema::hasTable('spendings')) {
            DB::table('spendings')->update([
                'gross_amount' => DB::raw('amount'),
                'net_amount' => DB::raw('amount'),
                'vat_amount' => 0,
                'status' => 'paid',
            ]);
        }

        if (Schema::hasTable('supplier_invoices')) {
            DB::table('supplier_invoices')->update([
                'gross_amount' => DB::raw('amount'),
                'net_amount' => DB::raw('amount'),
                'vat_amount' => 0,
            ]);
        }

        if (Schema::hasTable('wages')) {
            DB::table('wages')->update([
                'gross_amount' => DB::raw('amount'),
                'net_amount' => DB::raw('amount'),
                'vat_amount' => 0,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('wages', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('payroll_run_id');
            $table->dropColumn(['net_amount', 'vat_amount', 'gross_amount', 'period_start', 'period_end', 'payment_due_date', 'from_attendance', 'approved_at']);
        });

        Schema::table('supplier_invoices', function (Blueprint $table): void {
            $table->dropColumn(['net_amount', 'vat_amount', 'gross_amount', 'approved_at']);
        });

        Schema::table('bills', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('bank_account_id');
            $table->dropColumn(['net_amount', 'vat_amount', 'gross_amount', 'approved_at', 'paid_at']);
        });

        Schema::table('spendings', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('bank_account_id');
            $table->dropColumn(['net_amount', 'vat_amount', 'gross_amount', 'status', 'approved_at', 'paid_at']);
        });

        Schema::dropIfExists('finance_integration_connections');
        Schema::dropIfExists('finance_attachments');
        Schema::dropIfExists('finance_supplier_payments');
        Schema::dropIfExists('finance_income_entries');
        Schema::dropIfExists('finance_payroll_runs');
        Schema::dropIfExists('finance_bank_accounts');
    }
};
