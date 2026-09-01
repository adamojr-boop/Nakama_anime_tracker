<?php

namespace App\Livewire;

use App\Models\AnimeMetadata;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

class AnimeSearch extends Component
{
    public $searchQuery = '';
    public $results = [];

    public function updatedSearchQuery()
    {
        $query = trim($this->searchQuery);

        if (mb_strlen($query) < 3) {
            $this->results = [];
            return;
        }

        $cacheKey = 'anime-search:v2:' . sha1(mb_strtolower($query));
        if (Cache::has($cacheKey)) {
            $this->results = Cache::get($cacheKey);
            return;
        }

        $externalResults = $this->searchExternal($query);

        if ($externalResults !== null) {
            Cache::put($cacheKey, $externalResults, now()->addMinutes(5));
            $this->results = $externalResults;
            return;
        }

        $this->results = $this->searchLocal($query);
    }

    private function searchExternal(string $query): ?array
    {
        try {
            $response = Http::timeout(3)
                ->connectTimeout(1)
                ->get('https://api.jikan.moe/v4/anime', [
                'q' => $query,
                'limit' => 5
            ]);

            if ($response->successful()) {
                return $response->json('data', []);
            }

            throw new \RuntimeException('Jikan ha risposto con errore.');
        } catch (\Exception $e) {
            logger('Jikan Fallito. Attivazione Fallback su Kitsu API. Errore: ' . $e->getMessage());

            try {
                $kitsuResponse = Http::timeout(3)
                    ->connectTimeout(1)
                    ->get('https://kitsu.io/api/edge/anime', [
                    'filter[text]' => $query,
                    'page[limit]' => 5
                ]);

                if ($kitsuResponse->successful()) {
                    $kitsuData = $kitsuResponse->json()['data'] ?? [];

                    return array_map(function ($item) {
                        return [
                            'mal_id' => $item['id'],
                            'title' => $item['attributes']['canonicalTitle'] ?? 'Titolo Sconosciuto',
                            'type' => strtoupper($item['attributes']['showType'] ?? 'TV'),
                            'episodes' => $item['attributes']['episodeCount'] ?? '?',
                            'score' => number_format(($item['attributes']['averageRating'] ?? 0) / 10, 1), // Kitsu usa i centesimi (es. 83.4), lo portiamo in decimi (8.3)
                            'images' => [
                                'jpg' => [
                                    'small_image_url' => $item['attributes']['posterImage']['small'] ?? 'https://via.placeholder.com/40x55'
                                ]
                            ]
                        ];
                    }, $kitsuData);
                }
            } catch (\Exception $kitsuException) {
                logger('Anche Kitsu API è offline: ' . $kitsuException->getMessage());
            }
        }

        return null;
    }

    private function searchLocal(string $query): array
    {
        return AnimeMetadata::query()
            ->where('title', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(fn (AnimeMetadata $anime) => [
                'mal_id' => $anime->mal_id,
                'title' => $anime->title,
                'type' => 'TV',
                'episodes' => $anime->total_episodes ?? '?',
                'score' => 'N/D',
                'images' => [
                    'jpg' => ['small_image_url' => $anime->image_url ?? 'https://via.placeholder.com/40x55'],
                ],
            ])
            ->all();
    }

    public function render()
    {
        return view('components.anime.anime-search');
    }
}
