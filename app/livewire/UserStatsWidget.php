<?php

namespace App\Livewire;

use App\Models\EpisodeTracker;
use App\Services\BadgeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserStatsWidget extends Component
{
    protected $listeners = ['listUpdated' => '$refresh'];

    public function render()
    {
        $userId = Auth::id();

        if (!$userId) {
            return view('user_stats.user-stats-widget', [
                'stats' => null
            ]);
        }

        $trackers = EpisodeTracker::where('user_id', $userId)->get();

        $totalMinutes = 0;
        $totalEpisodesWatched = 0;
        $totalRewatches = 0;

        foreach ($trackers as $track) {
            $duration = $track->episode_duration ?? 24;
            // Episodi visti nella visione corrente
            $currentEpisodes = $track->watched_episodes ?? 0;
            // Episodi visti nei rewatch passati
            $rewatchedEpisodes = $track->total_rewatched_episodes ?? 0;

            $animeEpisodesCount = $currentEpisodes + $rewatchedEpisodes;

            $totalEpisodesWatched += $animeEpisodesCount;
            $totalMinutes += ($animeEpisodesCount * $duration);
            $totalRewatches += ($track->rewatch_count ?? 0);
        }
        // Calcolo Giorni, Ore e Minuti
        $days = floor($totalMinutes / 1440);
        $hours = floor(($totalMinutes % 1440) / 60);
        $minutes = $totalMinutes % 60;
        // Logica Livello Gamificato basato sui Rewatch
        $levelInfo = $this->calculateRewatchLevel($totalRewatches);

        $stats = [
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
            'total_episodes' => $totalEpisodesWatched,
            'total_rewatches' => $totalRewatches,
            'level' => $levelInfo['level'],
            'title' => $levelInfo['title'],
            'badge' => $levelInfo['badge'],
            'next_target' => $levelInfo['next_target'],
            'progress_percent' => $levelInfo['progress_percent'],
        ];

        return view('user_stats.user-stats-widget', compact('stats'));
    }

    private function calculateRewatchLevel($rewatches)
    {
        if ($rewatches >= 10) {
            return [
                'level' => 4,
                'title' => 'Otaku Supremo',
                'badge' => '💎',
                'next_target' => 'Max Level!',
                'progress_percent' => 100,
            ];
        } elseif ($rewatches >= 5) {
            return [
                'level' => 3,
                'title' => 'Fan Accanito',
                'badge' => '🥇',
                'next_target' => 10,
                'progress_percent' => min(100, (($rewatches - 5) / 5) * 100),
            ];
        } elseif ($rewatches >= 3) {
            return [
                'level' => 2,
                'title' => 'Nostalgico',
                'badge' => '🥈',
                'next_target' => 5,
                'progress_percent' => min(100, (($rewatches - 3) / 2) * 100),
            ];
        } elseif ($rewatches >= 1) {
            return [
                'level' => 1,
                'title' => 'Casual Re-watcher',
                'badge' => '🥉',
                'next_target' => 3,
                'progress_percent' => min(100, (($rewatches - 1) / 2) * 100),
            ];
        }

        return [
            'level' => 0,
            'title' => 'Nuovo Spettatore',
            'badge' => '🌱',
            'next_target' => 1,
            'progress_percent' => 0,
        ];
    }

    public function incrementEpisode($malId)
    {
        // ... Logica esistente per incrementare l'episodio nel DB/Lista ...

        // Tracciamento Binge-Watching & Trofei
        $badgeService = app(BadgeService::class);
        $newBadges = $badgeService->trackBingeSession(auth()->user(), $malId, 1);

        if (!empty($newBadges)) {
            session()->flash('badge_unlocked', '🏆 Nuovo Trofeo Maratona Sbloccato: ' . implode(', ', $newBadges));
        }
    }
}
