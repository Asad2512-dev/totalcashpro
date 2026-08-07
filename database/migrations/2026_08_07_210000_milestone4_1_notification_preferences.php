<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('category', 50);
            $table->boolean('email_enabled')->default(true);
            $table->boolean('database_enabled')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'category']);
        });

        Schema::table('login_histories', function (Blueprint $table): void {
            if (! Schema::hasColumn('login_histories', 'event_type')) {
                $table->string('event_type', 20)->default('login')->after('success');
            }
        });
    }

    public function down(): void
    {
        Schema::table('login_histories', function (Blueprint $table): void {
            if (Schema::hasColumn('login_histories', 'event_type')) {
                $table->dropColumn('event_type');
            }
        });

        Schema::dropIfExists('notification_preferences');
    }
};
