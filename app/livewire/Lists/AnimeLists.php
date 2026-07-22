<?php

namespace App\Livewire\Lists;

use App\Models\EpisodeTracker;
use App\Services\AnimeMetadataService;
use Livewire\Component;

class AnimeLists extends Component
{
    public string $currentFilter = 'watching';

    public function setFilter(string $filter): void
    {
        $allowedFilters = ['watching', 'plan_to_watch', 'completed', 'dropped', 'my-lists'];

        if (!in_array($filter, $allowedFilters, true)) {
            return;
        }

        $this->currentFilter = $filter;
    }

    public function render()
    {
        $userId = auth()->id();

        if ($this->currentFilter === 'my-lists') {
            $animeList = [];
        } else {
            $trackers = EpisodeTracker::query()
                ->where('user_id', $userId)
                ->where('status', $this->currentFilter)
                ->latest('updated_at')
                ->get(['mal_id', 'watched_episodes', 'status', 'rewatch_count', 'episode_duration']);

            $animeList = app(AnimeMetadataService::class)->hydrateTrackers($trackers);
        }

        return view('components.lists.anime-lists', [
            'animeList' => $animeList,
        ])->layout('components.layouts.app');
    }
}
