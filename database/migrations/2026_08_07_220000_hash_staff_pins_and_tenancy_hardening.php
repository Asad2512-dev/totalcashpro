<?php

declare(strict_types=1);

use App\Models\User;
use App\Support\Security\StaffPinHasher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'pin_hash')) {
                $table->string('pin_hash')->nullable()->after('phone');
            }
        });

        if (Schema::hasColumn('users', 'pin_code')) {
            User::query()
                ->whereNotNull('pin_code')
                ->where('pin_code', '!=', '')
                ->each(function (User $user): void {
                    $user->forceFill([
                        'pin_hash' => StaffPinHasher::hash((string) $user->pin_code),
                        'pin_code' => null,
                    ])->saveQuietly();
                });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'pin_hash')) {
                $table->dropColumn('pin_hash');
            }
        });
    }
};
