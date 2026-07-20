<?php

namespace App\Http\Controllers;

use App\Models\CustomList;
use App\Models\EpisodeTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

        if (Auth::check()) {
            $trackersByMalId = EpisodeTracker::where('user_id', Auth::id())
                ->whereIn('mal_id', $animeIds)
                ->get()
                ->keyBy('mal_id')
                ->all();
        }

        foreach ($animeIds as $id) {
            $tracker = $trackersByMalId[$id] ?? null;

            try {
                $response = Http::timeout(2)->get("https://api.jikan.moe/v4/anime/{$id}");
                if ($response->successful()) {
                    $apiData = $response->json()['data'];
                    $animeList[] = [
                        'mal_id' => $id,
                        'title' => $apiData['title'],
                        'image' => $apiData['images']['jpg']['image_url'] ?? 'https://via.placeholder.com/150x210',
                        'status' => $tracker->status ?? null,
                        'rewatch_count' => $tracker->rewatch_count ?? 0,
                    ];
                    continue;
                }

                throw new \Exception();
            } catch (\Exception $e) {
                try {
                    $kitsuResponse = Http::timeout(2)->get("https://kitsu.io/api/edge/anime/{$id}");
                    if ($kitsuResponse->successful()) {
                        $item = $kitsuResponse->json()['data'] ?? null;
                        if ($item) {
                            $animeList[] = [
                                'mal_id' => $id,
                                'title' => $item['attributes']['canonicalTitle'],
                                'image' => $item['attributes']['posterImage']['medium'] ?? 'https://via.placeholder.com/150x210',
                                'status' => $tracker->status ?? null,
                                'rewatch_count' => $tracker->rewatch_count ?? 0,
                            ];
                        }
                    }
                } catch (\Exception $kitsuException) {
                    $animeList[] = [
                        'mal_id' => $id,
                        'title' => "Anime #{$id} (Offline)",
                        'image' => 'https://via.placeholder.com/150x210',
                        'status' => $tracker->status ?? null,
                        'rewatch_count' => $tracker->rewatch_count ?? 0,
                    ];
                }
            }
        }

        return $animeList;
    }
}