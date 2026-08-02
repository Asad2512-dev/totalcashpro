<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_requests', function (Blueprint $table): void {
            if (! Schema::hasColumn('access_requests', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('organization_id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('access_requests', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('access_requests', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('additional_notes');
            }
        });

        Schema::table('coupons', function (Blueprint $table): void {
            if (! Schema::hasColumn('coupons', 'plan_id')) {
                $table->foreignId('plan_id')->nullable()->after('status')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('coupons', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('plan_id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('discounts', function (Blueprint $table): void {
            if (! Schema::hasColumn('discounts', 'grant_type')) {
                $table->string('grant_type')->default('percentage')->after('type');
            }
        });

        Schema::table('media', function (Blueprint $table): void {
            if (! Schema::hasColumn('media', 'folder')) {
                $table->string('folder')->nullable()->after('collection')->index();
            }
        });

        if (! Schema::hasTable('support_ticket_replies')) {
            Schema::create('support_ticket_replies', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->text('body');
                $table->boolean('is_internal')->default(false);
                $table->timestamps();
            });
        }

        Schema::table('announcements', function (Blueprint $table): void {
            if (! Schema::hasColumn('announcements', 'target_plan_slug')) {
                $table->string('target_plan_slug')->nullable()->after('audience');
            }
            if (! Schema::hasColumn('announcements', 'organization_id')) {
                $table->foreignId('organization_id')->nullable()->after('target_plan_slug')->constrained()->nullOnDelete();
            }
        });

        Schema::table('notifications', function (Blueprint $table): void {
            if (! Schema::hasColumn('notifications', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('read_at')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_replies');

        Schema::table('notifications', function (Blueprint $table): void {
            if (Schema::hasColumn('notifications', 'archived_at')) {
                $table->dropColumn('archived_at');
            }
        });

        Schema::table('announcements', function (Blueprint $table): void {
            if (Schema::hasColumn('announcements', 'organization_id')) {
                $table->dropConstrainedForeignId('organization_id');
            }
            if (Schema::hasColumn('announcements', 'target_plan_slug')) {
                $table->dropColumn('target_plan_slug');
            }
        });

        Schema::table('media', function (Blueprint $table): void {
            if (Schema::hasColumn('media', 'folder')) {
                $table->dropColumn('folder');
            }
        });

        Schema::table('discounts', function (Blueprint $table): void {
            if (Schema::hasColumn('discounts', 'grant_type')) {
                $table->dropColumn('grant_type');
            }
        });

        Schema::table('coupons', function (Blueprint $table): void {
            if (Schema::hasColumn('coupons', 'organization_id')) {
                $table->dropConstrainedForeignId('organization_id');
            }
            if (Schema::hasColumn('coupons', 'plan_id')) {
                $table->dropConstrainedForeignId('plan_id');
            }
        });

        Schema::table('access_requests', function (Blueprint $table): void {
            if (Schema::hasColumn('access_requests', 'reviewed_by')) {
                $table->dropConstrainedForeignId('reviewed_by');
            }
            foreach (['reviewed_at', 'admin_notes'] as $column) {
                if (Schema::hasColumn('access_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
