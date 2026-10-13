<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('commission_rate', 4, 2)->nullable()->after('total');
            $table->unsignedInteger('commission_amount')->nullable()->after('commission_rate');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'commission_amount']);
        });
    }
};
