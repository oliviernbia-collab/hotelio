<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('payout_transfer_id')->nullable()->after('commission_amount');
            $table->string('payout_status')->nullable()->after('payout_transfer_id'); // pending|success|error
            $table->string('payout_failure_reason')->nullable()->after('payout_status');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['payout_transfer_id', 'payout_status', 'payout_failure_reason']);
        });
    }
};
