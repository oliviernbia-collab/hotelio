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
        Schema::table('factures', function (Blueprint $table) {
            $table->decimal('taxe_hoteliere', 10, 2)->default(0)->after('tva');
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete()->after('hotel');
            $table->string('mode_paiement')->nullable()->after('statut');
            $table->text('notes')->nullable()->after('mode_paiement');
        });
    }

    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
            $table->dropColumn(['taxe_hoteliere', 'reservation_id', 'mode_paiement', 'notes']);
        });
    }
};
