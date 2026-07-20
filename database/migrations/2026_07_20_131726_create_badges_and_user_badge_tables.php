<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabella di catalogo per i Badge
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // es. 'hype_warrior', 'social_butterfly'
            $table->string('name'); // es. 'Simulcast Warrior'
            $table->text('description'); // es. 'Hai visto un episodio entro 24 ore dal rilascio!'
            $table->string('icon')->default('🏆'); // Emoji o path icona
            $table->string('category'); // 'hype', 'social', 'rewatch', ecc.
            $table->timestamps();
        });
        // Tabella Pivot per i badge sbloccati dagli utenti
        Schema::create('user_badge', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('unlocked_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'badge_id']); // Previene sblocchi duplicati
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badge');
        Schema::dropIfExists('badges');
    }
};
