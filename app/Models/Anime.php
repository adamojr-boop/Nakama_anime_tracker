<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    use HasFactory;

    protected $table = 'anime_metadata';

    protected $fillable = [
        'title',
        'slug',
        'synopsis',
        'cover_image',
        'genre',
        'rating',
    ];

    public function favoritedByProfiles()
    {
        return $this->belongsToMany(Profile::class, 'anime_user_favorites')
            ->withPivot('custom_tag', 'order')
            ->withTimestamps();
    }
}
