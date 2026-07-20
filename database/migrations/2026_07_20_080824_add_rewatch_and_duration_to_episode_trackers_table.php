<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('episode_trackers', function (Blueprint $table) {
            $table->unsignedInteger('rewatch_count')->default(0)->after('status');
            $table->unsignedInteger('episode_duration')->default(24)->after('rewatch_count');
            $table->unsignedInteger('total_rewatched_episodes')->default(0)->after('episode_duration');
        });
    }

    public function down(): void
    {
        Schema::table('episode_trackers', function (Blueprint $table) {
            $table->dropColumn(['rewatch_count', 'episode_duration', 'total_rewatched_episodes']);
        });
    }
};
