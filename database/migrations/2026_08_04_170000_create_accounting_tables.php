<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('vendor')->nullable();
            $table->string('category', 40)->default('other');
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('due_date');
            $table->string('status', 20)->default('pending');
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'due_date']);
        });

        Schema::create('spendings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('category', 40)->default('other');
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('spent_date');
            $table->string('payment_method', 40)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['organization_id', 'spent_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spendings');
        Schema::dropIfExists('bills');
    }
};
