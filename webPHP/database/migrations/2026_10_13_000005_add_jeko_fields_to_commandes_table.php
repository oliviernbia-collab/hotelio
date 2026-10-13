<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            if (! Schema::hasColumn('commandes', 'jeko_reference')) {
                $table->string('jeko_reference')->nullable()->after('payment_reference');
            }
            if (! Schema::hasColumn('commandes', 'jeko_payment_request_id')) {
                $table->string('jeko_payment_request_id')->nullable()->after('jeko_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            if (Schema::hasColumn('commandes', 'jeko_reference')) {
                $table->dropColumn('jeko_reference');
            }
            if (Schema::hasColumn('commandes', 'jeko_payment_request_id')) {
                $table->dropColumn('jeko_payment_request_id');
            }
        });
    }
};
