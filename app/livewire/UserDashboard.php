<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EpisodeTracker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserDashboard extends Component
{
    public $animeList = [];
    public $currentFilter = 'watching'; // Filtro iniziale: mostriamo gli anime in corso
    public function setFilter($filter)
    {
        $this->currentFilter = $filter;
        $this->loadAnimeList();
    }

    public function mount()
    {
        $this->loadAnimeList();
    }

    public function loadAnimeList()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        // 1. Recuperiamo i record dal nostro database locale filtrati per lo stato scelto
        $trackedAnime = EpisodeTracker::where('user_id', Auth::id())
            ->where('status', $this->currentFilter)
            ->latest('updated_at')
            ->get();

        $enrichedList = [];
        // 2. Per ogni anime tracciato nel DB, recuperiamo i dettagli grafici dall'API
        foreach ($trackedAnime as $track) {
            try {// Tentativo principale con Jikan
                $response = Http::timeout(2)->get("https://api.jikan.moe/v4/anime/{$track->mal_id}");

                if ($response->successful()) {
                    $apiData = $response->json()['data'];
                    $enrichedList[] = [
                        'mal_id' => $track->malId ?? $track->mal_id,
                        'watched_episodes' => $track->watched_episodes,
                        'total_episodes' => $apiData['episodes'] ?? '?',
                        'title' => $apiData['title'],
                        'image' => $apiData['images']['jpg']['image_url'] ?? 'https://via.placeholder.com/150x210',
                    ];
                    continue;
                }
                throw new \Exception();
            } catch (\Exception $e) {
                // Fallback su Kitsu se Jikan è lento/in errore
                try {
                    $kitsuResponse = Http::timeout(2)->get("https://kitsu.io/api/edge/anime/{$track->mal_id}");
                    if ($kitsuResponse->successful()) {
                        $item = $kitsuResponse->json()['data'] ?? null;
                        if ($item) {
                            $enrichedList[] = [
                                'mal_id' => $track->mal_id,
                                'watched_episodes' => $track->watched_episodes,
                                'total_episodes' => $item['attributes']['episodeCount'] ?? '?',
                                'title' => $item['attributes']['canonicalTitle'],
                                'image' => $item['attributes']['posterImage']['medium'] ?? 'https://via.placeholder.com/150x210',
                            ];
                        }
                    }
                } catch (\Exception $kitsuEx) { // Se entrambi i server falliscono, mostriamo comunque la riga senza rompere la pagina
                    $enrichedList[] = [
                        'mal_id' => $track->mal_id,
                        'watched_episodes' => $track->watched_episodes,
                        'total_episodes' => '?',
                        'title' => "Anime #" . $track->mal_id . " (Dettagli offline)",
                        'image' => 'https://via.placeholder.com/150x210',
                    ];
                }
            }
        }

        $this->animeList = $enrichedList;
    }

    public function render()
    {
        return view('livewire.user-dashboard')
            ->layout('components.layouts.app');
    }
}
