<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('business_name');
            $table->string('owner_name');
            $table->string('email');
            $table->string('phone');
            $table->string('business_address')->nullable();
            $table->string('country');
            $table->string('business_type');
            $table->string('number_of_employees');
            $table->string('selected_plan');
            $table->text('additional_notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_requests');
    }
};
