<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episode_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('anime_mal_id');
            $table->unsignedInteger('episode_number');
            $table->text('body');
            $table->foreignId('parent_id')->nullable()->constrained('episode_comments')->cascadeOnDelete(); // Per risposte nidificate
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episode_comments');
    }
};
