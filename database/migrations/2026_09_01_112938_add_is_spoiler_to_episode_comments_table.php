<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('episode_comments', function (Blueprint $table) {
            $table->boolean('is_spoiler')->default(false)->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('episode_comments', function (Blueprint $table) {
            $table->dropColumn('is_spoiler');
        });
    }
};
