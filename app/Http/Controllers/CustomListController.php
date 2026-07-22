<?php

namespace App\Http\Controllers;

use App\Models\CustomList;
use App\Models\EpisodeTracker;
use App\Services\AnimeMetadataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomListController extends Controller
{
    public function show(Request $request, $id)
    {
        $query = CustomList::query()->where('id', $id);

        if (Auth::check()) {
            $query->where(function ($innerQuery) {
                $innerQuery->where('user_id', Auth::id())
                    ->orWhere('is_public', true);
            });
        } else {
            $query->where('is_public', true);
        }

        $list = $query->firstOrFail();
        $animeList = $this->loadAnimeList($list);

        return view('components.lists.show-custom-list', [
            'list' => $list,
            'animeList' => $animeList,
        ]);
    }

    private function loadAnimeList(CustomList $list): array
    {
        $animeList = [];
        $animeIds = $list->anime_ids ?? [];
        $trackersByMalId = [];
        $metadataByMalId = app(AnimeMetadataService::class)
            ->getForMalIds($animeIds)
            ->keyBy('mal_id');

        if (Auth::check()) {
            $trackersByMalId = EpisodeTracker::where('user_id', Auth::id())
                ->whereIn('mal_id', $animeIds)
                ->get()
                ->keyBy('mal_id')
                ->all();
        }

        foreach ($animeIds as $id) {
            $tracker = $trackersByMalId[$id] ?? null;
            $meta = $metadataByMalId->get((int) $id);

            $animeList[] = [
                'mal_id' => (int) $id,
                'title' => $meta?->title ?? "Anime #{$id}",
                'image' => $meta?->image_url ?? 'https://via.placeholder.com/150x210',
                'status' => $tracker->status ?? null,
                'rewatch_count' => $tracker->rewatch_count ?? 0,
            ];
        }

        return $animeList;
    }
}