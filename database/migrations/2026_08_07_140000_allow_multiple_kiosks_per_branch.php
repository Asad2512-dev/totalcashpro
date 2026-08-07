<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branch_kiosks', function (Blueprint $table): void {
            $table->dropForeign(['branch_id']);
        });

        Schema::table('branch_kiosks', function (Blueprint $table): void {
            $table->dropUnique(['branch_id']);
            $table->index(['branch_id', 'is_enabled']);
            $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('branch_kiosks', function (Blueprint $table): void {
            $table->dropForeign(['branch_id']);
        });

        Schema::table('branch_kiosks', function (Blueprint $table): void {
            $table->dropIndex(['branch_id', 'is_enabled']);
            $table->unique('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->cascadeOnDelete();
        });
    }
};
