<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'banner',
        'avatar_frame',
        'banner_pattern',
        'bio',
        'social_links',
    ];

    // --- AGGIUNGI QUESTO BLOCCO CASTS ---
    protected $casts = [
        'social_links' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoriteAnimes()
    {
        return $this->hasMany(FavoriteAnime::class)->orderBy('slot_position', 'asc');
    }
}
