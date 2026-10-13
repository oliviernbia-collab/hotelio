<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name');
            $table->string('city');
            $table->string('quartier');
            $table->unsignedTinyInteger('stars')->default(3);
            $table->unsignedSmallInteger('rooms')->default(12);
            $table->string('category');
            $table->string('status')->default('Ouvert');
            $table->decimal('rating', 3, 1)->default(4.5);
            $table->unsignedInteger('price')->default(210000);
            $table->string('emoji', 4)->nullable();
            $table->string('color', 20)->nullable();
            $table->string('manager')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('revenue')->default(0);
            $table->unsignedTinyInteger('occupancy')->default(0);
            $table->unsignedInteger('checkins')->default(0);
            $table->unsignedInteger('checkouts')->default(0);
            $table->text('description')->nullable();
            $table->date('opened_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
