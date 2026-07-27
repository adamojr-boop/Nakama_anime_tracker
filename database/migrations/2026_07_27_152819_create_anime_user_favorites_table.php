<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anime_user_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('anime_id')->constrained()->onDelete('cascade');
            $table->string('custom_tag')->nullable(); // es. "Masterpiece", "Top Tier", "Sci-Fi Fav"
            $table->integer('order')->default(1); // Per definire la posizione da 1 a 4
            $table->timestamps();

            // Garantisce che lo stesso anime non sia aggiunto due volte allo stesso profilo
            $table->unique(['profile_id', 'anime_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anime_user_favorites');
    }
};
