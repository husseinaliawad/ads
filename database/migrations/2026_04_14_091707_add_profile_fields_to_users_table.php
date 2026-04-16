<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('city')->nullable()->after('phone');
            $table->string('avatar_path')->nullable()->after('city');
            $table->enum('role', ['user', 'admin'])->default('user')->after('password');
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');

            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn([
                'phone',
                'city',
                'avatar_path',
                'role',
                'is_active',
                'phone_verified_at',
            ]);
        });
    }
};
