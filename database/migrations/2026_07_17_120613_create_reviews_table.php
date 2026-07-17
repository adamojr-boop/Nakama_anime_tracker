<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('mal_id'); // ID dell'anime recensito
            $table->tinyInteger('rating')->unsigned(); // Voto da 1 a 10
            $table->text('comment');

            $table->boolean('is_spoiler')->default(false); // Fondamentale per la community!

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
