<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Révocation des jetons JWT de l'app mobile (backendMobile) : tout jeton émis avant cette date
 * est refusé. Mis à jour par « Se déconnecter de tous les appareils » — l'équivalent de la
 * suppression des jetons Sanctum côté web (Api\AuthController::logoutAll).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('tokens_valid_after')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tokens_valid_after');
        });
    }
};
