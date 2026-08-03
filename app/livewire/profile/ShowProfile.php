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

    public function mount(?int $userId = null): void
    {
        $this->user = $userId
            ? User::with('profile')->findOrFail($userId)
            : auth()->user()->load('profile');
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['showcase', 'stats', 'badges'])) {
            $this->activeTab = $tab;
        }
    }

    public function render()
    {
        $profile = $this->user?->profile;

        $trackers = $this->user
            ? EpisodeTracker::query()
                ->where('user_id', $this->user->id)
                ->get()
            : collect();

        // Unica fonte dati: episode_trackers, con metadata locale per card e titoli.
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
            'watchingAnimes' => $watchingAnimes,
            'stats' => $stats,
            'badges' => $badges,
        ])->layout('layouts.app');
    }
}
