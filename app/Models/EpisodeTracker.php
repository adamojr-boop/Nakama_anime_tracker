<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    // Dentro la classe EpisodeTracker:
    public function animeMetadata(): BelongsTo
    {
        // Collega mal_id o anime_id di EpisodeTracker con mal_id di AnimeMetadata
        return $this->belongsTo(AnimeMetadata::class, 'mal_id', 'mal_id');
    }
}
