<?php

namespace App\Jobs;

use App\Models\AnimeMetadata;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class RefreshAnimeMetadata implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $malIds)
    {
    }

    public function handle(): void
    {
        $ids = collect($this->malIds)
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        foreach ($ids as $malId) {
            $existing = AnimeMetadata::where('mal_id', $malId)->first();

            $hasGenres = is_array($existing?->genres) && count(array_filter($existing->genres)) > 0;
            $hasStudios = is_array($existing?->studios) && count(array_filter($existing->studios)) > 0;

            if (
                $existing
                && $existing->last_synced_at
                && $existing->last_synced_at->gt(now()->subHours(24))
                && $hasGenres
                && $hasStudios
            ) {
                continue;
            }

            $payload = $this->fetchFromJikan($malId) ?? $this->fetchFromKitsu($malId);

            if (!$payload) {
                AnimeMetadata::updateOrCreate(
                    ['mal_id' => $malId],
                    [
                        'title' => "Anime #{$malId}",
                        'image_url' => 'https://via.placeholder.com/300x420?text=Anime',
                        'total_episodes' => null,
                        'genres' => [],
                        'studios' => [],
                        'source' => 'offline',
                        'last_synced_at' => Carbon::now(),
                    ]
                );

                continue;
            }

            AnimeMetadata::updateOrCreate(
                ['mal_id' => $malId],
                [
                    'title' => $payload['title'],
                    'image_url' => $payload['image_url'],
                    'total_episodes' => $payload['total_episodes'],
                    'genres' => $payload['genres'] ?? [],
                    'studios' => $payload['studios'] ?? [],
                    'source' => $payload['source'],
                    'last_synced_at' => Carbon::now(),
                ]
            );
        }
    }

    private function fetchFromJikan(int $malId): ?array
    {
        try {
            $response = Http::retry(2, 500)->timeout(8)->get("https://api.jikan.moe/v4/anime/{$malId}");

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json('data');

            if (!$data) {
                return null;
            }

            return [
                'title' => $data['title'] ?? "Anime #{$malId}",
                'image_url' => $data['images']['jpg']['image_url'] ?? 'https://via.placeholder.com/300x420?text=Anime',
                'total_episodes' => isset($data['episodes']) && is_numeric($data['episodes']) ? (int) $data['episodes'] : null,
                'genres' => collect($data['genres'] ?? [])->pluck('name')->filter()->values()->all(),
                'studios' => collect($data['studios'] ?? [])->pluck('name')->filter()->values()->all(),
                'source' => 'jikan',
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function fetchFromKitsu(int $malId): ?array
    {
        try {
            $response = Http::retry(2, 500)->timeout(8)->get("https://kitsu.io/api/edge/anime/{$malId}?include=categories,animeProductions.producer");

            if (!$response->successful()) {
                return null;
            }

            $item = $response->json('data');
            $included = collect($response->json('included', []));

            if (!$item) {
                return null;
            }

            $genres = $included
                ->where('type', 'categories')
                ->map(static fn ($entry) => $entry['attributes']['title'] ?? null)
                ->filter()
                ->unique()
                ->values()
                ->all();

            $studios = $included
                ->whereIn('type', ['producers', 'animeProductions'])
                ->map(static function ($entry) {
                    return $entry['attributes']['name']
                        ?? $entry['attributes']['canonicalName']
                        ?? $entry['attributes']['title']
                        ?? null;
                })
                ->filter()
                ->unique()
                ->values()
                ->all();

            return [
                'title' => $item['attributes']['canonicalTitle'] ?? "Anime #{$malId}",
                'image_url' => $item['attributes']['posterImage']['medium'] ?? 'https://via.placeholder.com/300x420?text=Anime',
                'total_episodes' => isset($item['attributes']['episodeCount']) && is_numeric($item['attributes']['episodeCount']) ? (int) $item['attributes']['episodeCount'] : null,
                'genres' => $genres,
                'studios' => $studios,
                'source' => 'kitsu',
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }
}
