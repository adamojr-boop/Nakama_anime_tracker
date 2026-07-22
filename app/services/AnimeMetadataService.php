<?php

namespace App\Services;

use App\Jobs\RefreshAnimeMetadata;
use App\Models\AnimeMetadata;
use Illuminate\Support\Collection;

class AnimeMetadataService
{
    public function getForMalIds(array $malIds): Collection
    {
        $ids = collect($malIds)
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $metadata = AnimeMetadata::query()
            ->whereIn('mal_id', $ids->all())
            ->get()
            ->keyBy('mal_id');

        $missingOrStale = $ids->filter(function (int $id) use ($metadata) {
            $item = $metadata->get($id);

            return !$item || !$item->last_synced_at || $item->last_synced_at->lte(now()->subHours(24));
        })->values();

        if ($missingOrStale->isNotEmpty()) {
            RefreshAnimeMetadata::dispatchAfterResponse($missingOrStale->all())
                ->onConnection('sync');
        }

        return $metadata;
    }

    public function hydrateTrackers(Collection $trackers): array
    {
        $metadata = $this->getForMalIds($trackers->pluck('mal_id')->all());

        return $trackers->map(function ($track) use ($metadata) {
            $meta = $metadata->get((int) $track->mal_id);
            $total = $meta?->total_episodes;
            $watched = (int) $track->watched_episodes;
            $percent = $total && $total > 0 ? (int) round(($watched / $total) * 100) : 0;
            $duration = (int) ($track->episode_duration ?? 24);
            $remainingEp = $total && $total > 0 ? max(0, $total - $watched) : 0;
            $remainingMinutes = $remainingEp * $duration;

            return [
                'mal_id' => (int) $track->mal_id,
                'title' => $meta?->title ?? "Anime #{$track->mal_id}",
                'image' => $meta?->image_url ?? 'https://via.placeholder.com/300x420?text=Anime',
                'total_episodes' => $total,
                'watched_episodes' => $watched,
                'status' => $track->status,
                'rewatch_count' => (int) ($track->rewatch_count ?? 0),
                'percent' => $percent,
                'remaining_formatted' => $remainingEp > 0
                    ? ((int) floor($remainingMinutes / 60) > 0 ? (int) floor($remainingMinutes / 60) . 'h ' : '') . ($remainingMinutes % 60) . 'm'
                    : null,
            ];
        })->all();
    }
}
