<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anime_metadata', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mal_id')->unique();
            $table->string('title')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('total_episodes')->nullable();
            $table->string('source')->nullable();
            $table->timestamp('last_synced_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anime_metadata');
    }
};
