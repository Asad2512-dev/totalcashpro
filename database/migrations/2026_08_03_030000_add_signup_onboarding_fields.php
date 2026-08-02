<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table): void {
            if (! Schema::hasColumn('organizations', 'business_type')) {
                $table->string('business_type')->nullable()->after('timezone');
            }
        });

        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'onboarding_completed_at')) {
                $table->timestamp('onboarding_completed_at')->nullable()->after('email_verified_at');
            }
        });

        Schema::table('subscriptions', function (Blueprint $table): void {
            if (! Schema::hasColumn('subscriptions', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable()->after('cancelled_at');
            }
            if (! Schema::hasColumn('subscriptions', 'stripe_subscription_id')) {
                $table->string('stripe_subscription_id')->nullable()->after('stripe_customer_id');
            }
            if (! Schema::hasColumn('subscriptions', 'pending_plan_id')) {
                $table->foreignId('pending_plan_id')->nullable()->after('plan_id')->constrained('plans')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table): void {
            if (Schema::hasColumn('subscriptions', 'pending_plan_id')) {
                $table->dropConstrainedForeignId('pending_plan_id');
            }
            $table->dropColumn(array_filter([
                Schema::hasColumn('subscriptions', 'stripe_customer_id') ? 'stripe_customer_id' : null,
                Schema::hasColumn('subscriptions', 'stripe_subscription_id') ? 'stripe_subscription_id' : null,
            ]));
        });

        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'onboarding_completed_at')) {
                $table->dropColumn('onboarding_completed_at');
            }
        });

        Schema::table('organizations', function (Blueprint $table): void {
            if (Schema::hasColumn('organizations', 'business_type')) {
                $table->dropColumn('business_type');
            }
        });
    }
};
