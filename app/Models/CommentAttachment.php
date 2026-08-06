<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentAttachment extends Model
{
    protected $fillable = [
        'episode_comment_id',
        'file_path',
        'type',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(EpisodeComment::class, 'episode_comment_id');
    }
}
