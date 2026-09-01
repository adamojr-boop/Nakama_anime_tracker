<?php

namespace App\Services;

use App\Models\AnimeMetadata;
use Illuminate\Support\Facades\Http;

class StreamingAvailabilityService
{
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
                ->get('/search/tv', ['api_key' => $apiKey, 'query' => $title]);

            $match = $search->successful() ? $search->json('results.0') : null;

            if (! $match || ! isset($match['id'])) {
                return $empty;
            }

            $tmdbId = (int) $match['id'];
            $region = config('services.tmdb.region', 'IT');
            $providers = Http::baseUrl('https://api.themoviedb.org/3')
                ->retry(2, 500)
                ->timeout(8)
                ->get("/tv/{$tmdbId}/watch/providers", ['api_key' => $apiKey]);
            $videos = Http::baseUrl('https://api.themoviedb.org/3')
                ->retry(2, 500)
                ->timeout(8)
                ->get("/tv/{$tmdbId}/videos", ['api_key' => $apiKey]);

            $flatrate = $providers->successful()
                ? collect($providers->json("results.{$region}.flatrate", []))
                : collect();
            $watchUrl = $providers->successful()
                ? $providers->json("results.{$region}.link")
                : null;
            $trailer = $videos->successful()
                ? collect($videos->json('results', []))
                    ->first(fn (array $video) => $video['site'] === 'YouTube' && $video['type'] === 'Trailer')
                : null;

            return [
                'tmdb_id' => $tmdbId,
                'providers' => $flatrate->map(fn (array $provider) => [
                    'name' => $provider['provider_name'],
                    'logo_path' => $provider['logo_path'] ?? null,
                ])->all(),
                'trailer_url' => isset($trailer['key']) ? "https://www.youtube.com/watch?v={$trailer['key']}" : null,
                'watch_url' => $watchUrl ?? $this->tmdbWatchUrl($tmdbId),
            ];
        } catch (\Throwable) {
            return $empty;
        }
    }

    private function payloadFromMetadata(AnimeMetadata $metadata): array
    {
        return [
            'tmdb_id' => $metadata->tmdb_id,
            'providers' => $metadata->streaming_providers ?? [],
            'trailer_url' => $metadata->trailer_url,
            'watch_url' => $metadata->tmdb_id ? $this->tmdbWatchUrl($metadata->tmdb_id) : null,
        ];
    }

    private function tmdbWatchUrl(int $tmdbId): string
    {
        $region = config('services.tmdb.region', 'IT');
        $locale = strtolower($region) . '-' . strtoupper($region);

        return "https://www.themoviedb.org/tv/{$tmdbId}/watch?locale={$locale}";
    }
}