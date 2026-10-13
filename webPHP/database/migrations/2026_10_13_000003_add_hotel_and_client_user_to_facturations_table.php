<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Colonnes déjà ajoutées par backendMobile (Node) au démarrage dans certains
        // environnements — guard pour rester idempotent sur une base déjà à jour.
        Schema::table('facturations', function (Blueprint $table) {
            if (! Schema::hasColumn('facturations', 'hotel_id')) {
                $table->foreignId('hotel_id')->nullable()->after('numero')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('facturations', 'client_user_id')) {
                $table->foreignId('client_user_id')->nullable()->after('hotel_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('facturations', function (Blueprint $table) {
            if (Schema::hasColumn('facturations', 'hotel_id')) {
                $table->dropConstrainedForeignId('hotel_id');
            }
            if (Schema::hasColumn('facturations', 'client_user_id')) {
                $table->dropConstrainedForeignId('client_user_id');
            }
        });
    }
};
