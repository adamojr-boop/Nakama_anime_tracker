<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpisodeTracker extends Model
{
    protected $fillable = ['user_id', 'mal_id', 'watched_episodes', 'status'];
}
