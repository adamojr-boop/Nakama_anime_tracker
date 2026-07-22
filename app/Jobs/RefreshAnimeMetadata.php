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

            if ($existing && $existing->last_synced_at && $existing->last_synced_at->gt(now()->subHours(24))) {
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
                    'source' => $payload['source'],
                    'last_synced_at' => Carbon::now(),
                ]
            );
        }
    }

    private function fetchFromJikan(int $malId): ?array
    {
        try {
            $response = Http::timeout(3)->get("https://api.jikan.moe/v4/anime/{$malId}");

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
                'source' => 'jikan',
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function fetchFromKitsu(int $malId): ?array
    {
        try {
            $response = Http::timeout(3)->get("https://kitsu.io/api/edge/anime/{$malId}");

            if (!$response->successful()) {
                return null;
            }

            $item = $response->json('data');

            if (!$item) {
                return null;
            }

            return [
                'title' => $item['attributes']['canonicalTitle'] ?? "Anime #{$malId}",
                'image_url' => $item['attributes']['posterImage']['medium'] ?? 'https://via.placeholder.com/300x420?text=Anime',
                'total_episodes' => isset($item['attributes']['episodeCount']) && is_numeric($item['attributes']['episodeCount']) ? (int) $item['attributes']['episodeCount'] : null,
                'source' => 'kitsu',
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }
}
