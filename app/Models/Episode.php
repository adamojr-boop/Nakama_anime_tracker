<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'anime_id',
        'title',
        'episode_number',
        'air_date_utc',
        'status',
    ];

    protected $casts = [
        'air_date_utc' => 'datetime',
    ];
}
