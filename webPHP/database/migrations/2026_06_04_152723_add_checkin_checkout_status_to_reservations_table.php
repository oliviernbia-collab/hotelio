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
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('checkin_at')->nullable()->after('checkout');
            $table->timestamp('checkout_at')->nullable()->after('checkin_at');
            $table->string('notes')->nullable()->after('nationality');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['checkin_at', 'checkout_at', 'notes']);
        });
    }
};
