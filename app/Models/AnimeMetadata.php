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
        'source',
        'last_synced_at',
    ];

    protected $casts = [
        'genres' => 'array',
        'studios' => 'array',
        'last_synced_at' => 'datetime',
    ];
}
