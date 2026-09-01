<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    // app/Models/Profile.php

    protected $fillable = [
        'user_id',
        'bio',
        'avatar',
        'banner',
        'banner_pattern',
        'avatar_frame',
        'social_links',
    ];

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
