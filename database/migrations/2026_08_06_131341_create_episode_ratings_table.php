<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episode_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('anime_mal_id');
            $table->unsignedInteger('episode_number');
            $table->unsignedTinyInteger('rating'); // Es. da 1 a 10
            $table->string('favorite_character')->nullable();
            $table->string('emotion', 50)->nullable(); // euphoria, sadness, shock, anger, hype, ecc.
            $table->timestamps();

            // Un utente può lasciare un solo voto/reazione per singolo episodio
            $table->unique(['user_id', 'anime_mal_id', 'episode_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episode_ratings');
    }
};
