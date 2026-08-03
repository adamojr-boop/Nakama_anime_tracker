<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Profile;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1) . Str::substr($initials, -1)
            : $initials;
    }

    // Gli anime che l'utente sta seguendo/ha visto
    // In app/Models/User.php

    public function animes()
    {
        // Indichiamo a Laravel:
        // 1. Tabella pivot: 'anime_user'
        // 2. Foreign Key utente: 'user_id'
        // 3. Foreign Key anime: 'mal_id'
        return $this->belongsToMany(
            Anime::class,
            'anime_user',
            'user_id',
            'mal_id'
        )->withPivot('status', 'episodes_watched')->withTimestamps();
    }

    public function watchingAnimes()
    {
        return $this->episodeTrackers()->where('status', 'watching');
    }

    public function episodeTrackers(): HasMany
    {
        return $this->hasMany(EpisodeTracker::class);
    }

    // Le recensioni scritte dall'utente
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badge')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class)->withDefault([
            'banner_pattern' => 'pattern-1',
            'avatar_frame'   => 'default',
            'bio'            => 'Appassionato/a di Anime & Manga!',
            'social_links'   => []
        ]);
    }
}
