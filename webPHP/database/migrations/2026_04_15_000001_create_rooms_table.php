<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->unsignedTinyInteger('floor')->default(1);
            $table->string('type')->default('Standard');
            $table->string('status')->default('disponible');
            $table->unsignedInteger('price')->default(0);
            $table->unsignedTinyInteger('capacity')->default(2);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['hotel_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
