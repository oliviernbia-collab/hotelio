<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('title', 200)->nullable();
            $table->text('comment');
            $table->string('status', 20)->default('approved');
            $table->timestamps();
            $table->unique(['hotel_id', 'user_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('reviews');
    }
};
