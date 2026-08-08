<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table): void {
            if (! Schema::hasColumn('suppliers', 'trading_name')) {
                $table->string('trading_name')->nullable()->after('name');
            }
            if (! Schema::hasColumn('suppliers', 'postcode')) {
                $table->string('postcode', 20)->nullable()->after('address');
            }
            if (! Schema::hasColumn('suppliers', 'website')) {
                $table->string('website')->nullable()->after('postcode');
            }
            if (! Schema::hasColumn('suppliers', 'tax_number')) {
                $table->string('tax_number', 50)->nullable()->after('website');
            }
            if (! Schema::hasColumn('suppliers', 'payment_terms')) {
                $table->string('payment_terms', 100)->nullable()->after('tax_number');
            }
            if (! Schema::hasColumn('suppliers', 'currency')) {
                $table->string('currency', 3)->default('GBP')->after('payment_terms');
            }
            if (! Schema::hasColumn('suppliers', 'lead_time_days')) {
                $table->unsignedSmallInteger('lead_time_days')->default(0)->after('currency');
            }
            if (! Schema::hasColumn('suppliers', 'min_order_value')) {
                $table->decimal('min_order_value', 12, 2)->default(0)->after('lead_time_days');
            }
        });

        if (! Schema::hasTable('supplier_contacts')) {
            Schema::create('supplier_contacts', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('role', 50)->nullable();
                $table->string('email')->nullable();
                $table->string('phone', 30)->nullable();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
                $table->index(['supplier_id', 'is_primary']);
            });
        }

        if (! Schema::hasTable('supplier_price_history')) {
            Schema::create('supplier_price_history', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
                $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
                $table->decimal('unit_cost', 12, 2);
                $table->string('unit', 20)->default('pcs');
                $table->date('effective_from');
                $table->date('effective_until')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['supplier_id', 'inventory_item_id', 'effective_from'], 'supplier_price_hist_idx');
            });
        }

        Schema::table('supplier_products', function (Blueprint $table): void {
            if (! Schema::hasColumn('supplier_products', 'unit')) {
                $table->string('unit', 20)->default('pcs')->after('pack_size');
            }
            if (! Schema::hasColumn('supplier_products', 'vat_rate')) {
                $table->decimal('vat_rate', 5, 2)->default(20)->after('unit_cost');
            }
            if (! Schema::hasColumn('supplier_products', 'order_multiple')) {
                $table->unsignedInteger('order_multiple')->default(1)->after('moq');
            }
            if (! Schema::hasColumn('supplier_products', 'active')) {
                $table->boolean('active')->default(true)->after('is_primary');
            }
            if (! Schema::hasColumn('supplier_products', 'effective_from')) {
                $table->date('effective_from')->nullable()->after('active');
            }
            if (! Schema::hasColumn('supplier_products', 'effective_until')) {
                $table->date('effective_until')->nullable()->after('effective_from');
            }
        });

        if (! Schema::hasTable('procurement_settings')) {
            Schema::create('procurement_settings', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->decimal('quantity_tolerance_percent', 5, 2)->default(2);
                $table->decimal('price_tolerance_percent', 5, 2)->default(1);
                $table->boolean('auto_create_bill_on_match')->default(true);
                $table->timestamps();
                $table->unique('organization_id');
            });
        }

        Schema::table('purchase_orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('purchase_orders', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('ordered_at');
            }
            if (! Schema::hasColumn('purchase_orders', 'sent_by')) {
                $table->foreignId('sent_by')->nullable()->after('sent_at')->constrained('users')->nullOnDelete();
            }
        });

        if (! Schema::hasTable('purchase_order_amendments')) {
            Schema::create('purchase_order_amendments', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('amended_by')->constrained('users')->cascadeOnDelete();
                $table->string('field');
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->text('reason');
                $table->timestamps();
                $table->index(['purchase_order_id', 'created_at']);
            });
        }

        Schema::table('goods_received_notes', function (Blueprint $table): void {
            if (! Schema::hasColumn('goods_received_notes', 'grn_number')) {
                $table->string('grn_number', 30)->nullable()->after('id');
            }
            if (! Schema::hasColumn('goods_received_notes', 'delivery_id')) {
                $table->foreignId('delivery_id')->nullable()->after('purchase_order_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('goods_received_notes', 'status')) {
                $table->string('status', 30)->default('completed')->after('notes');
            }
        });

        if (! Schema::hasTable('goods_returns')) {
            Schema::create('goods_returns', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
                $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
                $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('goods_received_note_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('inventory_item_id')->nullable()->constrained()->nullOnDelete();
                $table->decimal('quantity', 12, 3);
                $table->string('reason', 50);
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('supplier_invoice_lines')) {
            Schema::create('supplier_invoice_lines', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('supplier_invoice_id')->constrained()->cascadeOnDelete();
                $table->foreignId('purchase_order_line_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('inventory_item_id')->nullable()->constrained()->nullOnDelete();
                $table->string('description');
                $table->decimal('quantity', 12, 3);
                $table->decimal('unit_cost', 12, 2);
                $table->decimal('vat_rate', 5, 2)->default(20);
                $table->decimal('line_total', 12, 2);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('invoice_matches')) {
            Schema::create('invoice_matches', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
                $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
                $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('goods_received_note_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('supplier_invoice_id')->constrained()->cascadeOnDelete();
                $table->string('status', 30)->default('pending');
                $table->decimal('po_quantity', 12, 3)->default(0);
                $table->decimal('grn_quantity', 12, 3)->default(0);
                $table->decimal('invoice_quantity', 12, 3)->default(0);
                $table->decimal('po_amount', 12, 2)->default(0);
                $table->decimal('grn_amount', 12, 2)->default(0);
                $table->decimal('invoice_amount', 12, 2)->default(0);
                $table->decimal('quantity_variance', 12, 3)->default(0);
                $table->decimal('price_variance', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
                $table->index(['organization_id', 'status']);
                $table->index(['supplier_invoice_id']);
            });
        }

        if (! Schema::hasTable('supplier_disputes')) {
            Schema::create('supplier_disputes', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
                $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
                $table->foreignId('supplier_invoice_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('invoice_match_id')->nullable()->constrained()->nullOnDelete();
                $table->decimal('disputed_amount', 12, 2);
                $table->string('status', 30)->default('open');
                $table->text('reason')->nullable();
                $table->text('resolution_notes')->nullable();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('deliveries', function (Blueprint $table): void {
            if (! Schema::hasColumn('deliveries', 'priority')) {
                $table->string('priority', 20)->default('normal')->after('status');
            }
            if (! Schema::hasColumn('deliveries', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('deliveries', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejection_reason');
            }
            if (! Schema::hasColumn('deliveries', 'pickup_discrepancy_qty')) {
                $table->decimal('pickup_discrepancy_qty', 12, 3)->nullable()->after('pickup_notes');
            }
            if (! Schema::hasColumn('deliveries', 'pickup_discrepancy_reason')) {
                $table->text('pickup_discrepancy_reason')->nullable()->after('pickup_discrepancy_qty');
            }
            if (! Schema::hasColumn('deliveries', 'failure_reason')) {
                $table->string('failure_reason', 50)->nullable()->after('failed_at');
            }
            if (! Schema::hasColumn('deliveries', 'awaiting_receiving')) {
                $table->boolean('awaiting_receiving')->default(false)->after('delivered_at');
            }
        });

        if (! Schema::hasTable('delivery_events')) {
            Schema::create('delivery_events', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('delivery_id')->constrained()->cascadeOnDelete();
                $table->string('event', 50);
                $table->text('notes')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['delivery_id', 'created_at']);
            });
        }

        if (! Schema::hasTable('delivery_proofs')) {
            Schema::create('delivery_proofs', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('delivery_id')->constrained()->cascadeOnDelete();
                $table->string('type', 30);
                $table->string('path')->nullable();
                $table->text('metadata')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('riders', function (Blueprint $table): void {
            if (! Schema::hasColumn('riders', 'status')) {
                $table->string('status', 20)->default('active')->after('user_id');
            }
            if (! Schema::hasColumn('riders', 'employee_reference')) {
                $table->string('employee_reference', 50)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('riders', 'vehicle_type')) {
                $table->string('vehicle_type', 50)->nullable()->after('vehicle');
            }
            if (! Schema::hasColumn('riders', 'vehicle_registration')) {
                $table->string('vehicle_registration', 20)->nullable()->after('vehicle_type');
            }
            if (! Schema::hasColumn('riders', 'notes')) {
                $table->text('notes')->nullable()->after('vehicle_registration');
            }
        });

        Schema::table('supplier_invoices', function (Blueprint $table): void {
            if (! Schema::hasColumn('supplier_invoices', 'currency')) {
                $table->string('currency', 3)->default('GBP')->after('gross_amount');
            }
            if (! Schema::hasColumn('supplier_invoices', 'reference')) {
                $table->string('reference')->nullable()->after('currency');
            }
        });

        try {
            Schema::table('supplier_invoices', function (Blueprint $table): void {
                $table->unique(['organization_id', 'supplier_id', 'invoice_no'], 'supplier_invoice_unique');
            });
        } catch (\Throwable) {
            // Index may already exist.
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_proofs');
        Schema::dropIfExists('delivery_events');
        Schema::dropIfExists('supplier_disputes');
        Schema::dropIfExists('invoice_matches');
        Schema::dropIfExists('supplier_invoice_lines');
        Schema::dropIfExists('goods_returns');
        Schema::dropIfExists('purchase_order_amendments');
        Schema::dropIfExists('procurement_settings');
        Schema::dropIfExists('supplier_price_history');
        Schema::dropIfExists('supplier_contacts');
        Schema::dropIfExists('procurement_settings');
    }
};
