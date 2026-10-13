<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('room');
            $table->date('checkin');
            $table->date('checkout');
            $table->unsignedTinyInteger('nights')->default(1);
            $table->unsignedInteger('price')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->string('status')->default('confirmée');
            $table->string('channel')->nullable();
            $table->boolean('paid')->default(false);
            $table->string('payment_method')->nullable();
            $table->string('nationality')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
