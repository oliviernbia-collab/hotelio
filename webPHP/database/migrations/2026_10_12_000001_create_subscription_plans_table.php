<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->unsignedInteger('price_monthly');
            $table->decimal('commission_rate', 4, 2);
            $table->unsignedInteger('max_hotels')->nullable();
            $table->json('features')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('subscription_plans')->insert([
            [
                'slug' => 'starter',
                'name' => 'Starter',
                'price_monthly' => 5000,
                'commission_rate' => 10.00,
                'max_hotels' => 1,
                'features' => json_encode(['Réservations, chambres, fiches clients et suivi de base']),
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'business',
                'name' => 'Business',
                'price_monthly' => 10000,
                'commission_rate' => 7.00,
                'max_hotels' => 5,
                'features' => json_encode(['Tous les modules de gestion courante', 'Facturation', 'Statistiques avancées']),
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'premium',
                'name' => 'Premium',
                'price_monthly' => 15000,
                'commission_rate' => 5.00,
                'max_hotels' => null,
                'features' => json_encode(['Fonctionnalités avancées', 'Rapports détaillés', 'Assistance prioritaire', 'Accompagnement']),
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
