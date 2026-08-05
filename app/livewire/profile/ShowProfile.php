<?php

namespace App\Livewire\Profile;

use App\Models\EpisodeTracker;
use App\Services\AnimeMetadataService;
use Livewire\Component;
use App\Models\User;

class ShowProfile extends Component
{
    public ?User $user = null;
    public string $activeTab = 'showcase';

    protected $listeners = [
        'profile-updated' => '$refresh',
        'refreshProfile'  => '$refresh'
    ];

    public function mount(?int $userId = null): void
    {
        $this->user = $userId
            ? User::with(['profile.favoriteAnimes'])->findOrFail($userId)
            : auth()->user()->load(['profile.favoriteAnimes']);
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['showcase', 'stats', 'badges'])) {
            $this->activeTab = $tab;
        }
    }

    public function render()
    {
        // 1. Ricarica il profilo ed i relativi preferiti
        // In App\Livewire\Profile\ShowProfile.php -> render()

        $profile = $this->user?->profile()->with('favoriteAnimes')->first();

        $favoritesBySlot = [];
        if ($profile && $profile->favoriteAnimes) {
            // Raccogliamo i mal_id per recuperare le immagini mancanti dai metadata
            $malIds = $profile->favoriteAnimes->pluck('mal_id')->filter()->toArray();
            $metadata = \App\Models\AnimeMetadata::whereIn('mal_id', $malIds)->get()->keyBy('mal_id');

            foreach ($profile->favoriteAnimes as $fav) {
                // Se image_url è vuoto nel preferito, lo prendiamo dal record metadata
                $meta = $metadata->get($fav->mal_id);
                $fav->image_display = $fav->image_url
                    ?? $meta?->image_url
                    ?? $meta?->image
                    ?? '/images/default-anime.jpg';

                $favoritesBySlot[$fav->slot_position] = $fav;
            }
        }

        $trackers = $this->user
            ? EpisodeTracker::query()
            ->where('user_id', $this->user->id)
            ->get()
            : collect();

        $watchingTrackers = $trackers->where('status', 'watching')->values();
        $watchingAnimes = collect(app(AnimeMetadataService::class)->hydrateTrackers($watchingTrackers));

        $completedCount = $trackers->where('status', 'completed')->count();
        $totalEpisodesWatched = (int) $trackers->sum('watched_episodes');

        $stats = [
            'total_completed' => $completedCount,
            'episodes_watched' => $totalEpisodesWatched,
            'time_watched_hours' => round(($totalEpisodesWatched * 24) / 60),
            'favorite_genre' => 'Shonen',
        ];

        $badges = [
            [
                'title' => 'Otaku Novizio',
                'description' => 'Hai completato i tuoi primi 5 anime.',
                'icon' => '🌸',
                'unlocked' => $completedCount >= 5,
            ],
            [
                'title' => 'Maratoneta',
                'description' => 'Hai visto più di 100 episodi.',
                'icon' => '⚡',
                'unlocked' => $totalEpisodesWatched >= 100,
            ],
            [
                'title' => 'Master of Shonen',
                'description' => 'Hai completato 20 serie Shonen.',
                'icon' => '🔥',
                'unlocked' => false,
            ],
            [
                'title' => 'Veterano Nakama',
                'description' => 'Iscritto da oltre 1 anno alla community.',
                'icon' => '👑',
                'unlocked' => $this->user?->created_at?->diffInYears(now()) >= 1,
            ],
        ];

        return view('components.profile.show-profile', [
            'profile' => $profile,
            'favoritesBySlot' => $favoritesBySlot, // Passiamo il nuovo array organizzato per slot
            'watchingAnimes' => $watchingAnimes,
            'stats' => $stats,
            'badges' => $badges,
        ])->layout('layouts.app');
    }
}
