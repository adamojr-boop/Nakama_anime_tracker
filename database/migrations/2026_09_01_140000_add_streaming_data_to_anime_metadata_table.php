<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anime_metadata', function (Blueprint $table) {
            $table->unsignedBigInteger('tmdb_id')->nullable()->after('mal_id');
            $table->json('streaming_providers')->nullable()->after('studios');
            $table->string('trailer_url')->nullable()->after('streaming_providers');
            $table->timestamp('streaming_synced_at')->nullable()->index()->after('trailer_url');
        });
    }

    public function down(): void
    {
        Schema::table('anime_metadata', function (Blueprint $table) {
            $table->dropColumn(['tmdb_id', 'streaming_providers', 'trailer_url', 'streaming_synced_at']);
        });
    }
};