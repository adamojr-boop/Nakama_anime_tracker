<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimeMetadata extends Model
{
    protected $table = 'anime_metadata';

    protected $fillable = [
        'mal_id',
        'title',
        'image_url',
        'total_episodes',
        'genres',
        'studios',
        'tmdb_id',
        'streaming_providers',
        'trailer_url',
        'streaming_synced_at',
        'source',
        'last_synced_at',
    ];

    protected $casts = [
        'genres' => 'array',
        'studios' => 'array',
        'streaming_providers' => 'array',
        'last_synced_at' => 'datetime',
        'streaming_synced_at' => 'datetime',
    ];
}
