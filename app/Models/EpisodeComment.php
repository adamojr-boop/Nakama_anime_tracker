<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EpisodeComment extends Model
{
    protected $fillable = [
        'user_id',
        'anime_mal_id',
        'episode_number',
        'body',
        'is_spoiler', 
        'parent_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(CommentAttachment::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(EpisodeComment::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(EpisodeComment::class, 'parent_id');
    }
}
