<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anime_metadata', function (Blueprint $table) {
            $table->json('genres')->nullable()->after('total_episodes');
            $table->json('studios')->nullable()->after('genres');
        });
    }

    public function down(): void
    {
        Schema::table('anime_metadata', function (Blueprint $table) {
            $table->dropColumn(['genres', 'studios']);
        });
    }
};
