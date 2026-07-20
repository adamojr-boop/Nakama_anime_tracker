<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpisodeTracker extends Model
{
    protected $fillable = [
        'user_id',
        'mal_id',
        'watched_episodes',
        'status',
        'watched_details',
        'rewatch_count',
        'episode_duration',
        'total_rewatched_episodes',
    ];
    // Forza Laravel a trasformare automaticamente il JSON del DB in un array PHP
    protected $casts = [
        'watched_details' => 'array',
    ];
}
