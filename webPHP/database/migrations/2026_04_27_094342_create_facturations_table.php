<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * php artisan migrate
     */
    public function up(): void
    {
        Schema::create('facturations', function (Blueprint $table) {
            $table->id();

            // Numéro unique de facturation : FAC-2025-0001
            $table->string('numero', 20)->unique();

            // Informations principales
            $table->string('client', 255);
            $table->string('prestation', 500);

            // Dates
            $table->date('date_facture');
            $table->date('date_echeance');

            // Montants
            $table->decimal('montant',     15, 2)->default(0);
            $table->decimal('montant_paye', 15, 2)->default(0);

            // Statut de paiement
            $table->enum('statut', ['paid', 'pending', 'overdue', 'partial', 'draft'])
                  ->default('pending');

            // Mode de paiement
            $table->string('mode_paiement', 100)->default('Espèces');

            // Notes / conditions
            $table->text('notes')->nullable();

            // Lignes de prestation stockées en JSON
            // Format : [{"d":"Description","q":2,"p":150000}, ...]
            $table->json('lignes')->nullable();

            $table->timestamps();

            // Index pour améliorer les performances des filtres fréquents
            $table->index('statut');
            $table->index('client');
            $table->index('date_facture');
            $table->index(['statut', 'date_echeance']); // requêtes "en retard"
        });
    }

    /**
     * php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('facturations');
    }
};
