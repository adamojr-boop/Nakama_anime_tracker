<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_animes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('mal_id'); // ID univoco di MyAnimeList / Jikan
            $table->string('title');
            $table->string('image_url');
            $table->string('custom_tag')->nullable(); // es. "Masterpiece", "Top Tier"
            $table->tinyInteger('slot_position'); // 1, 2, 3 o 4
            $table->timestamps();

            // Garantisce un solo anime per slot per ciascun profilo
            $table->unique(['profile_id', 'slot_position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_animes');
    }
};
