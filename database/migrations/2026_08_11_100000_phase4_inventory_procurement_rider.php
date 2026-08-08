<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('inventory_items', 'unit')) {
                $table->string('unit', 20)->default('pcs')->after('packaging');
            }
            if (! Schema::hasColumn('inventory_items', 'par_level')) {
                $table->unsignedInteger('par_level')->default(0)->after('stock_limit');
            }
            if (! Schema::hasColumn('inventory_items', 'min_level')) {
                $table->unsignedInteger('min_level')->default(0)->after('par_level');
            }
            if (! Schema::hasColumn('inventory_items', 'max_level')) {
                $table->unsignedInteger('max_level')->default(0)->after('min_level');
            }
            if (! Schema::hasColumn('inventory_items', 'order_multiple')) {
                $table->unsignedInteger('order_multiple')->default(1)->after('max_level');
            }
            if (! Schema::hasColumn('inventory_items', 'pack_size')) {
                $table->unsignedInteger('pack_size')->default(1)->after('order_multiple');
            }
            if (! Schema::hasColumn('inventory_items', 'lead_time_days')) {
                $table->unsignedSmallInteger('lead_time_days')->default(0)->after('pack_size');
            }
        });

        if (! Schema::hasTable('inventory_settings')) {
            Schema::create('inventory_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('stocktake_weekday')->default(0);
            $table->time('stocktake_time')->default('18:00:00');
            $table->boolean('stocktake_reminders')->default(true);
            $table->timestamps();
            $table->unique('organization_id');
            });
        }

        if (! Schema::hasTable('supplier_products')) {
            Schema::create('supplier_products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->string('supplier_sku')->nullable();
            $table->unsignedInteger('pack_size')->default(1);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('moq', 12, 2)->default(0);
            $table->unsignedSmallInteger('lead_time_days')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->unique(['supplier_id', 'inventory_item_id']);
            });
        }

        if (! Schema::hasTable('inventory_stocktakes')) {
            Schema::create('inventory_stocktakes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->string('status', 30)->default('draft');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('client_reference')->nullable();
            $table->string('idempotency_key')->nullable();
            $table->string('device_id')->nullable();
            $table->string('source', 30)->default('web');
            $table->timestamps();
            $table->index(['organization_id', 'branch_id', 'week_start']);
            $table->index(['status', 'branch_id']);
            });
        }

        if (! Schema::hasTable('inventory_stocktake_items')) {
            Schema::create('inventory_stocktake_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inventory_stocktake_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('system_qty', 12, 3)->default(0);
            $table->decimal('counted_qty', 12, 3)->nullable();
            $table->decimal('difference_qty', 12, 3)->nullable();
            $table->decimal('par_level', 12, 3)->default(0);
            $table->decimal('on_order_qty', 12, 3)->default(0);
            $table->decimal('suggested_order_qty', 12, 3)->default(0);
            $table->decimal('ordered_qty', 12, 3)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('excluded_from_order')->default(false);
            $table->string('override_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['inventory_stocktake_id', 'inventory_item_id'], 'stocktake_item_unique');
            });
        }

        if (! Schema::hasTable('inventory_transactions')) {
            Schema::create('inventory_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->decimal('quantity', 12, 3);
            $table->decimal('quantity_before', 12, 3);
            $table->decimal('quantity_after', 12, 3);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['inventory_item_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
            });
        }

        if (! Schema::hasTable('riders')) {
            Schema::create('riders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('branch_ids')->nullable();
            $table->string('phone')->nullable();
            $table->string('vehicle')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['organization_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('deliveries')) {
            Schema::create('deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 30)->default('assigned');
            $table->timestamp('expected_pickup_at')->nullable();
            $table->timestamp('expected_delivery_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('at_supplier_at')->nullable();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('out_for_delivery_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('pickup_notes')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['rider_id', 'status']);
            $table->index(['purchase_order_id']);
            });
        }

        Schema::table('purchase_order_lines', function (Blueprint $table): void {
            if (! Schema::hasColumn('purchase_order_lines', 'snapshot_unit_cost')) {
                $table->decimal('snapshot_unit_cost', 12, 2)->nullable()->after('unit_cost');
            }
            if (! Schema::hasColumn('purchase_order_lines', 'snapshot_pack_size')) {
                $table->unsignedInteger('snapshot_pack_size')->nullable()->after('snapshot_unit_cost');
            }
            if (! Schema::hasColumn('purchase_order_lines', 'snapshot_supplier_sku')) {
                $table->string('snapshot_supplier_sku')->nullable()->after('snapshot_pack_size');
            }
            if (! Schema::hasColumn('purchase_order_lines', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('inventory_item_id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('goods_received_lines', function (Blueprint $table): void {
            if (! Schema::hasColumn('goods_received_lines', 'quantity_accepted')) {
                $table->decimal('quantity_accepted', 12, 3)->default(0)->after('quantity_missing');
            }
        });
    }

    public function down(): void
    {
        Schema::table('goods_received_lines', function (Blueprint $table): void {
            $table->dropColumn('quantity_accepted');
        });

        Schema::table('purchase_order_lines', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropColumn(['snapshot_unit_cost', 'snapshot_pack_size', 'snapshot_supplier_sku']);
        });

        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('riders');
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_stocktake_items');
        Schema::dropIfExists('inventory_stocktakes');
        Schema::dropIfExists('supplier_products');
        Schema::dropIfExists('inventory_settings');

        Schema::table('inventory_items', function (Blueprint $table): void {
            $table->dropColumn(['unit', 'par_level', 'min_level', 'max_level', 'order_multiple', 'pack_size', 'lead_time_days']);
        });
    }
};
