<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rota_group_user')) {
            return;
        }

        Schema::create('rota_group_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rota_group_id')->constrained('rota_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id']);
            $table->unique(['rota_group_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rota_group_user');
    }
};
