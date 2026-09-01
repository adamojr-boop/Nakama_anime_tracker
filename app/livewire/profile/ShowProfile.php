<?php

namespace App\Livewire\Profile;

use App\Models\EpisodeTracker;
use App\Models\AnimeMetadata;
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

    public bool $isFollowing = false;
    public int $followersCount = 0;
    public int $followingCount = 0;

    public function mount(?int $userId = null): void
    {
        $this->user = $userId
            ? User::with(['profile.favoriteAnimes', 'followers', 'following'])->findOrFail($userId)
            : auth()->user()->load(['profile.favoriteAnimes', 'followers', 'following']);

        $this->updateFollowStats();
    }

    private function updateFollowStats(): void
    {
        $this->followersCount = $this->user->followers()->count();
        $this->followingCount = $this->user->following()->count();

        if (auth()->check()) {
            $this->isFollowing = auth()->user()->isFollowing($this->user);
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['showcase', 'stats', 'badges'])) {
            $this->activeTab = $tab;
        }
    }

    public function render()
    {
        $profile = $this->user?->profile()->with('favoriteAnimes')->first();

        $favoritesBySlot = [];
        if ($profile && $profile->favoriteAnimes) {
            $malIds = $profile->favoriteAnimes->pluck('mal_id')->filter()->toArray();
            $metadata = AnimeMetadata::whereIn('mal_id', $malIds)->get()->keyBy('mal_id');

            foreach ($profile->favoriteAnimes as $fav) {
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
            'favoritesBySlot' => $favoritesBySlot,
            'watchingAnimes' => $watchingAnimes,
            'stats' => $stats,
            'badges' => $badges,
            'chartData' => $this->getStatsDataProperty(), // <--- Cambiamo nome in chartData e chiamiamo direttamente il metodo
        ])->layout('components.layouts.app');
    }

    public bool $showFollowModal = false;
    public string $followModalType = 'followers';
    public $followModalUsers = [];

    public function openFollowModal(string $type): void
    {
        $this->followModalType = $type;
        $this->loadFollowModalUsers();
        $this->showFollowModal = true;
    }

    public function closeFollowModal(): void
    {
        $this->showFollowModal = false;
    }

    public function loadFollowModalUsers(): void
    {
        if ($this->followModalType === 'followers') {
            $this->followModalUsers = $this->user->followers()->with('profile')->get();
        } else {
            $this->followModalUsers = $this->user->following()->with('profile')->get();
        }
    }

    public function toggleFollowFromModal(int $targetUserId): void
    {
        if (!auth()->check()) {
            return;
        }

        $targetUser = User::findOrFail($targetUserId);
        auth()->user()->toggleFollow($targetUser);

        $this->updateFollowStats();
        $this->loadFollowModalUsers();
    }

    private function normalizeMetadataList(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }

        if (!is_string($value)) {
            return [];
        }

        $value = trim($value);
        if ($value === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return array_values(array_filter(array_map(static fn ($item) => trim((string) $item), $decoded)));
        }

        return array_values(array_filter(array_map(static fn ($item) => trim($item), explode(',', $value))));
    }

    public function getStatsDataProperty(): array
    {
        // 1. Estraiamo gli ID degli anime presenti nei tracker dell'utente
        $malIds = EpisodeTracker::where('user_id', $this->user?->id)
            ->pluck('mal_id')
            ->filter()
            ->unique();

        // 2. Query diretta su AnimeMetadata per recuperare generi e studi
        $animes = AnimeMetadata::whereIn('mal_id', $malIds)->get();

        $genresCount = [];
        $studiosCount = [];

        foreach ($animes as $anime) {
            // Conta Generi
            if (!empty($anime->genres)) {
                $genres = $this->normalizeMetadataList($anime->genres);
                foreach ($genres as $genre) {
                    $genreName = trim($genre);
                    if ($genreName) {
                        $genresCount[$genreName] = ($genresCount[$genreName] ?? 0) + 1;
                    }
                }
            }

            // Conta Studi
            if (!empty($anime->studios)) {
                $studios = $this->normalizeMetadataList($anime->studios);
                foreach ($studios as $studio) {
                    $studioName = trim($studio);
                    if ($studioName) {
                        $studiosCount[$studioName] = ($studiosCount[$studioName] ?? 0) + 1;
                    }
                }
            }
        }

        arsort($genresCount);
        arsort($studiosCount);

        $topGenres = array_slice($genresCount, 0, 5);
        $topStudios = array_slice($studiosCount, 0, 5);

        // Fallback non nullo: evita grafici visivamente vuoti quando non ci sono dati reali.
        if (empty($topGenres)) {
            $topGenres = ['Nessun dato' => 1];
        }
        if (empty($topStudios)) {
            $topStudios = ['Nessun dato' => 1];
        }

        return [
            'genres' => [
                'labels' => array_keys($topGenres),
                'data' => array_values($topGenres),
            ],
            'studios' => [
                'labels' => array_keys($topStudios),
                'data' => array_values($topStudios),
            ]
        ];
    }
}
