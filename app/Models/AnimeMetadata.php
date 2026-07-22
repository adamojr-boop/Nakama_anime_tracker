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
        'source',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];
}
