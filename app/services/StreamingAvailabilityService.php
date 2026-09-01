<?php

namespace App\Services;

use App\Models\AnimeMetadata;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class StreamingAvailabilityService
{
    /**
     * @return array{tmdb_id: int|null, providers: list<array{name: string, logo_path: string|null}>, trailer_url: string|null, watch_url: string|null}
     */
    public function getForAnime(int $malId, string $title): array
    {
        $metadata = AnimeMetadata::firstOrNew(['mal_id' => $malId]);

        if ($metadata->streaming_synced_at?->gt(now()->subDay())) {
            return $this->payloadFromMetadata($metadata);
        }

        $result = $this->fetch($title);

        $metadata->fill([
            'tmdb_id' => $result['tmdb_id'],
            'streaming_providers' => $result['providers'],
            'trailer_url' => $result['trailer_url'],
            'streaming_synced_at' => now(),
        ]);
        $metadata->save();

        return $result;
    }

    /**
     * @return array{tmdb_id: int|null, providers: list<array{name: string, logo_path: string|null}>, trailer_url: string|null, watch_url: string|null}
     */
    private function fetch(string $title): array
    {
        $empty = ['tmdb_id' => null, 'providers' => [], 'trailer_url' => null, 'watch_url' => null];
        $apiKey = config('services.tmdb.key');

        if (! filled($apiKey)) {
            return $empty;
        }

        try {
            $search = Http::baseUrl('https://api.themoviedb.org/3')
                ->retry(2, 500)
                ->timeout(8)
                ->connectTimeout(3)
                ->get('/search/tv', ['api_key' => $apiKey, 'query' => $title]);

            $match = $search->successful() ? $search->json('results.0') : null;

            if (! is_array($match) || ! isset($match['id'])) {
                return $empty;
            }

            $tmdbId = (int) $match['id'];
            $region = config('services.tmdb.region', 'IT');
            $responses = Http::pool(fn (Pool $pool) => [
                $pool->as('providers')
                    ->baseUrl('https://api.themoviedb.org/3')
                    ->retry(2, 500)
                    ->timeout(8)
                    ->connectTimeout(3)
                    ->get("/tv/{$tmdbId}/watch/providers", ['api_key' => $apiKey]),
                $pool->as('videos')
                    ->baseUrl('https://api.themoviedb.org/3')
                    ->retry(2, 500)
                    ->timeout(8)
                    ->connectTimeout(3)
                    ->get("/tv/{$tmdbId}/videos", ['api_key' => $apiKey]),
            ]);
            $providers = $responses['providers'] ?? null;
            $videos = $responses['videos'] ?? null;

            $flatrate = $providers instanceof Response && $providers->successful()
                ? $providers->json("results.{$region}.flatrate", [])
                : [];
            $watchUrl = $providers instanceof Response && $providers->successful()
                ? $providers->json("results.{$region}.link")
                : null;
            $videoResults = $videos instanceof Response && $videos->successful()
                ? $videos->json('results', [])
                : [];
            $trailerKey = null;

            if (is_array($videoResults)) {
                foreach ($videoResults as $video) {
                    if (is_array($video) && ($video['site'] ?? null) === 'YouTube' && ($video['type'] ?? null) === 'Trailer') {
                        $trailerKey = $video['key'] ?? null;
                        break;
                    }
                }
            }

            return [
                'tmdb_id' => $tmdbId,
                'providers' => $this->normalizeProviders($flatrate),
                'trailer_url' => is_string($trailerKey) ? "https://www.youtube.com/watch?v={$trailerKey}" : null,
                'watch_url' => is_string($watchUrl) ? $watchUrl : $this->tmdbWatchUrl($tmdbId),
            ];
        } catch (\Throwable) {
            return $empty;
        }
    }

    /**
     * @return array{tmdb_id: int|null, providers: list<array{name: string, logo_path: string|null}>, trailer_url: string|null, watch_url: string|null}
     */
    private function payloadFromMetadata(AnimeMetadata $metadata): array
    {
        return [
            'tmdb_id' => $metadata->tmdb_id,
            'providers' => $this->normalizeProviders($metadata->streaming_providers),
            'trailer_url' => $metadata->trailer_url,
            'watch_url' => $metadata->tmdb_id ? $this->tmdbWatchUrl($metadata->tmdb_id) : null,
        ];
    }

    /**
     * @return list<array{name: string, logo_path: string|null}>
     */
    private function normalizeProviders(mixed $providers): array
    {
        if (! is_array($providers)) {
            return [];
        }

        $normalized = [];
        foreach ($providers as $provider) {
            if (! is_array($provider)) {
                continue;
            }

            $name = $provider['name'] ?? $provider['provider_name'] ?? null;
            if (! is_string($name) || $name === '') {
                continue;
            }

            $logoPath = $provider['logo_path'] ?? null;
            $normalized[] = [
                'name' => $name,
                'logo_path' => is_string($logoPath) ? $logoPath : null,
            ];
        }

        return $normalized;
    }

    private function tmdbWatchUrl(int $tmdbId): string
    {
        $region = config('services.tmdb.region', 'IT');
        $locale = strtolower($region).'-'.strtoupper($region);

        return "https://www.themoviedb.org/tv/{$tmdbId}/watch?locale={$locale}";
    }
}
