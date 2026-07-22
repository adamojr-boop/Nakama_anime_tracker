<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BingeSession extends Model
{
    protected $fillable = [
        'user_id',
        'mal_id',
        'episodes_watched',
        'last_watched_at',
    ];

    protected $casts = [
        'last_watched_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
