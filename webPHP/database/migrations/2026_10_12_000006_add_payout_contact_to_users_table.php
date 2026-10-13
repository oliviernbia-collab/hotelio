<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('payout_method')->nullable()->after('two_fa_enabled'); // wave|orange_money|mtn|moov|djamo|bank
            $table->json('payout_identifier')->nullable()->after('payout_method'); // numéro mobile money OU RIB bancaire
            $table->string('jeko_contact_id')->nullable()->after('payout_identifier');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['payout_method', 'payout_identifier', 'jeko_contact_id']);
        });
    }
};
