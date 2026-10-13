<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Même structure que la table créée côté backendMobile (Node) : les deux
        // applications partagent cette table dans la même base MySQL. Le backend
        // mobile la crée lui-même au démarrage (CREATE TABLE IF NOT EXISTS), donc
        // elle peut déjà exister selon l'environnement.
        if (Schema::hasTable('hotel_services')) {
            return;
        }

        Schema::create('hotel_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category', 100)->default('Général');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->string('emoji', 20)->default('✨');
            $table->boolean('available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_services');
    }
};
