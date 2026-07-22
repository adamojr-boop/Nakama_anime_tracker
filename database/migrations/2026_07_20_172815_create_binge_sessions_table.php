<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('binge_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('mal_id');
            $table->integer('episodes_watched')->default(1);
            $table->timestamp('last_watched_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('binge_sessions');
    }
};