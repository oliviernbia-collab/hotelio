<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('status');
            $table->unsignedBigInteger('staff_id')->nullable()->after('permissions');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['permissions', 'staff_id']);
        });
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
