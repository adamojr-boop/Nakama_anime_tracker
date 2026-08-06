<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('episode_comment_id')->constrained()->cascadeOnDelete();
            $table->string('file_path'); // Percorso nello storage o URL esterno per le GIF
            $table->string('type')->default('image'); // 'image' o 'external_gif'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_attachments');
    }
};
