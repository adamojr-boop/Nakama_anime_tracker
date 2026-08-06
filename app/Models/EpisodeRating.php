<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpisodeRating extends Model
{
    protected $fillable = [
        'user_id',
        'anime_mal_id',
        'episode_number',
        'rating',
        'favorite_character',
        'emotion',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
