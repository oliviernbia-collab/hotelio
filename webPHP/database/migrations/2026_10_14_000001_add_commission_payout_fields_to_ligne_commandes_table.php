<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ligne_commandes', function (Blueprint $table) {
            if (! Schema::hasColumn('ligne_commandes', 'commission_rate')) {
                $table->decimal('commission_rate', 4, 2)->nullable()->after('prix');
            }
            if (! Schema::hasColumn('ligne_commandes', 'commission_amount')) {
                $table->unsignedInteger('commission_amount')->nullable()->after('commission_rate');
            }
            if (! Schema::hasColumn('ligne_commandes', 'payout_transfer_id')) {
                $table->string('payout_transfer_id')->nullable()->after('commission_amount');
            }
            if (! Schema::hasColumn('ligne_commandes', 'payout_status')) {
                $table->string('payout_status')->nullable()->after('payout_transfer_id'); // pending|success|error
            }
            if (! Schema::hasColumn('ligne_commandes', 'payout_failure_reason')) {
                $table->string('payout_failure_reason')->nullable()->after('payout_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ligne_commandes', function (Blueprint $table) {
            foreach (['commission_rate', 'commission_amount', 'payout_transfer_id', 'payout_status', 'payout_failure_reason'] as $col) {
                if (Schema::hasColumn('ligne_commandes', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
