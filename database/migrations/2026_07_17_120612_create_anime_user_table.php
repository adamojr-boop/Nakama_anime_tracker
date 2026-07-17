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
        Schema::create('anime_user', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('mal_id'); // ID univoco di MyAnimeList da Jikan API
            $table->integer('episodes_watched')->default(0);
            $table->enum('status', ['watching', 'completed', 'on_hold', 'plan_to_watch'])->default('watching');
            
            $table->timestamps();

            // Evita che lo stesso utente tracci due volte lo stesso anime
            $table->unique(['user_id', 'mal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime_user');
    }
};
