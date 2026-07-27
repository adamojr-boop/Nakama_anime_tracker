<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Profile;

class FavoriteAnime extends Model
{
    protected $fillable = [
        'profile_id',
        'mal_id',
        'title',
        'image_url',
        'custom_tag',
        'slot_position',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
