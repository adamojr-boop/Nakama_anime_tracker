<?php

namespace App\Livewire\Profile;

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

        // Recupera gli anime con stato 'watching' basandosi sulla tabella pivot
        $watchingAnimes = $this->user?->animes()
            ->wherePivot('status', 'watching')
            ->get() ?? collect();

        // Conteggi per le statistiche basati sulle colonne corrette della pivot
        $completedCount = $this->user?->animes()->wherePivot('status', 'completed')->count() ?? 0;
        $totalEpisodesWatched = $this->user?->animes()->sum('anime_user.episodes_watched') ?? 0;

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
