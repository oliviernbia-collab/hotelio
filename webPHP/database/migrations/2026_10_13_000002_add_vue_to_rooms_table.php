<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Colonne déjà ajoutée par backendMobile (Node) dans certains environnements
        // via une migration au démarrage — guard pour rester idempotent.
        if (Schema::hasColumn('rooms', 'vue')) {
            return;
        }

        Schema::table('rooms', function (Blueprint $table) {
            $table->string('vue')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('rooms', 'vue')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->dropColumn('vue');
            });
        }
    }
};
