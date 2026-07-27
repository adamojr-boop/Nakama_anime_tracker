<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Immagini
            $table->string('avatar')->nullable();
            $table->string('avatar_frame')->default('default'); // Bordo sbloccabile/badge
            $table->string('banner')->nullable();
            $table->string('banner_pattern')->default('pattern-1'); // Pattern di default
            // Bio & Social
            $table->text('bio')->nullable();
            $table->json('social_links')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
