<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('jeko_reference')->nullable()->unique()->after('payment_method');
            $table->string('jeko_payment_request_id')->nullable()->after('jeko_reference');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['jeko_reference', 'jeko_payment_request_id']);
        });
    }
};
