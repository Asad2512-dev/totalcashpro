<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('business_alert_rules')) {
            Schema::create('business_alert_rules', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->string('rule_type', 50);
                $table->decimal('threshold_value', 12, 2)->nullable();
                $table->decimal('threshold_percent', 5, 2)->nullable();
                $table->unsignedSmallInteger('threshold_days')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->unique(['organization_id', 'branch_id', 'rule_type'], 'alert_rule_unique');
            });
        }

        if (! Schema::hasTable('business_alerts')) {
            Schema::create('business_alerts', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->string('alert_type', 50);
                $table->string('priority', 20)->default('medium');
                $table->string('status', 20)->default('open');
                $table->string('title');
                $table->text('message');
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('action_url')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('acknowledged_at')->nullable();
                $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('resolved_at')->nullable();
                $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['organization_id', 'status', 'priority']);
                $table->index(['alert_type', 'reference_type', 'reference_id'], 'alert_ref_idx');
            });
        }

        if (! Schema::hasTable('budgets')) {
            Schema::create('budgets', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->unsignedSmallInteger('year');
                $table->unsignedTinyInteger('month')->nullable();
                $table->string('name');
                $table->string('currency', 3)->default('GBP');
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['organization_id', 'year', 'month']);
            });
        }

        if (! Schema::hasTable('budget_lines')) {
            Schema::create('budget_lines', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('budget_id')->constrained()->cascadeOnDelete();
                $table->string('category', 50);
                $table->decimal('amount', 12, 2)->default(0);
                $table->timestamps();
                $table->unique(['budget_id', 'category']);
            });
        }

        Schema::table('scheduled_reports', function (Blueprint $table): void {
            if (! Schema::hasColumn('scheduled_reports', 'name')) {
                $table->string('name')->nullable()->after('organization_id');
            }
            if (! Schema::hasColumn('scheduled_reports', 'report_type')) {
                $table->string('report_type', 40)->nullable()->after('saved_report_id');
            }
            if (! Schema::hasColumn('scheduled_reports', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('report_type')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('scheduled_reports', 'filters')) {
                $table->json('filters')->nullable()->after('branch_id');
            }
            if (! Schema::hasColumn('scheduled_reports', 'format')) {
                $table->string('format', 20)->default('email')->after('filters');
            }
            if (! Schema::hasColumn('scheduled_reports', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('format')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('scheduled_reports', function (Blueprint $table): void {
            foreach (['name', 'report_type', 'branch_id', 'filters', 'format', 'created_by'] as $col) {
                if (Schema::hasColumn('scheduled_reports', $col)) {
                    if ($col === 'branch_id' || $col === 'created_by') {
                        $table->dropConstrainedForeignId($col);
                    } else {
                        $table->dropColumn($col);
                    }
                }
            }
        });
        Schema::dropIfExists('budget_lines');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('business_alerts');
        Schema::dropIfExists('business_alert_rules');
    }
};
