<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EpisodeTracker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserDashboard extends Component
{
    protected $listeners = ['listUpdated' => 'loadAnimeList'];

    public $animeList = [];
    public $currentFilter = 'watching';

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

        if ($this->currentFilter === 'my-lists') {
            $this->animeList = [];
            return;
        }

        $trackedAnime = EpisodeTracker::where('user_id', Auth::id())
            ->where('status', $this->currentFilter)
            ->latest('updated_at')
            ->get();

        $enrichedList = [];

        foreach ($trackedAnime as $track) {
            try {
                $response = Http::timeout(2)->get("https://api.jikan.moe/v4/anime/{$track->mal_id}");
                if ($response->successful()) {
                    $apiData = $response->json()['data'];
                    $enrichedList[] = [
                        'mal_id' => $track->mal_id,
                        'watched_episodes' => $track->watched_episodes,
                        'total_episodes' => $apiData['episodes'] ?? '?',
                        'title' => $apiData['title'],
                        'image' => $apiData['images']['jpg']['image_url'] ?? 'https://via.placeholder.com/150x210',
                    ];
                    continue;
                }
                throw new \Exception();
            } catch (\Exception $e) {
                // 🌟 FALLBACK 1: KITSU API
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
                            continue;
                        }
                    }
                    throw new \Exception();
                } catch (\Exception $kitsuEx) {
                    // 🌟 FALLBACK 2: OFFLINE (Se le API sono entrambe giù, mostra comunque l'anime!)
                    $enrichedList[] = [
                        'mal_id' => $track->mal_id,
                        'watched_episodes' => $track->watched_episodes,
                        'total_episodes' => '?',
                        'title' => "Anime ID #{$track->mal_id} (Dati non caricati)",
                        'image' => 'https://via.placeholder.com/150x210',
                    ];
                }
            }
        }

        $this->animeList = $enrichedList;
    }

    public function render()
    {
        $this->loadAnimeList();

        return view('livewire.user-dashboard')
            ->layout('components.layouts.app');
    }
}
