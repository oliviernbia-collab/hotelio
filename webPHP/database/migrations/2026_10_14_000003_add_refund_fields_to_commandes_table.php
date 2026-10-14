<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('jeko_payment_request_id');
            $table->unsignedInteger('cancellation_fee_amount')->nullable()->after('cancelled_at'); // 5% du total
            $table->unsignedInteger('admin_fee_amount')->nullable()->after('cancellation_fee_amount'); // 4% du total, reste sur le compte Jèko plateforme
            $table->unsignedInteger('refund_amount')->nullable()->after('admin_fee_amount'); // 95% du total, viré au client
            $table->string('refund_transfer_id')->nullable()->after('refund_amount');
            $table->string('refund_status')->nullable()->after('refund_transfer_id'); // pending|success|error
            $table->string('refund_failure_reason')->nullable()->after('refund_status');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn([
                'cancelled_at',
                'cancellation_fee_amount',
                'admin_fee_amount',
                'refund_amount',
                'refund_transfer_id',
                'refund_status',
                'refund_failure_reason',
            ]);
        });
    }
};
