<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AnimeDetailsService
{
    public function getForMalId(int $malId): ?array
    {
        $cacheKey = "anime-details:{$malId}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $anime = $this->fetchFromJikan($malId) ?? $this->fetchFromKitsu($malId);

        if ($anime !== null) {
            Cache::put($cacheKey, $anime, now()->addHours(6));
        }

        return $anime;
    }

    private function fetchFromJikan(int $malId): ?array
    {
        try {
            $response = Http::timeout(3)
                ->connectTimeout(1)
                ->get("https://api.jikan.moe/v4/anime/{$malId}");

            return $response->successful() ? $response->json('data') : null;
        } catch (\Throwable $exception) {
            logger()->warning('Jikan anime detail request failed.', ['mal_id' => $malId, 'exception' => $exception->getMessage()]);

            return null;
        }
    }

    private function fetchFromKitsu(int $malId): ?array
    {
        try {
            $response = Http::timeout(3)
                ->connectTimeout(1)
                ->get("https://kitsu.io/api/edge/anime/{$malId}");
            $item = $response->successful() ? $response->json('data') : null;

            if (! $item) {
                return null;
            }

            return [
                'mal_id' => $malId,
                'title' => $item['attributes']['canonicalTitle'] ?? 'Titolo Sconosciuto',
                'synopsis' => $item['attributes']['synopsis'] ?? 'Trama non disponibile.',
                'type' => strtoupper($item['attributes']['showType'] ?? 'TV'),
                'episodes' => $item['attributes']['episodeCount'] ?? '?',
                'score' => number_format(($item['attributes']['averageRating'] ?? 0) / 10, 1),
                'images' => [
                    'jpg' => [
                        'large_image_url' => $item['attributes']['posterImage']['large'] ?? 'https://via.placeholder.com/225x320',
                    ],
                ],
            ];
        } catch (\Throwable $exception) {
            logger()->warning('Kitsu anime detail request failed.', ['mal_id' => $malId, 'exception' => $exception->getMessage()]);

            return null;
        }
    }
}
