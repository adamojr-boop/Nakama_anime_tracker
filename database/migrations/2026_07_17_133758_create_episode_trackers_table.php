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
        Schema::create('episode_trackers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('mal_id'); // L'ID dell'anime
            $table->integer('watched_episodes')->default(0); // Quanti episodi ha visto l'utente
            $table->string('status')->default('watching');
            // watching, completed, on_hold, dropped
            $table->timestamps();
            // Evita che lo stesso utente abbia più righe per lo stesso anime
            $table->unique(['user_id', 'mal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episode_trackers');
    }
};
