<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('episode_trackers', function (Blueprint $table) {
            // Aggiungiamo una colonna di tipo JSON per salvare i numeri degli episodi visti
            $table->json('watched_details')->nullable()->after('watched_episodes');
        });
    }

    public function down(): void
    {
        Schema::table('episode_trackers', function (Blueprint $table) {
            $table->dropColumn('watched_details');
        });
    }
};
