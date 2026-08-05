<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteAnime extends Model
{
    protected $table = 'favorite_animes';

    protected $fillable = [
        'profile_id',
        'slot_position',
        'mal_id',
        'title',
        'image_url', // Mettilo se presente nella migrazione, altrimenti rimuovilo
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
