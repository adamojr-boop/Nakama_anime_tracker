<?php

namespace App\Livewire;

use App\Models\EpisodeTracker;
use App\Services\AnimeMetadataService;
use Livewire\Component;

class AnimeRecommendationModal extends Component
{
    public int $availableTime = 30;
    public bool $includePlanToWatch = false;
    public ?array $recommendation = null;
    public ?string $errorMessage = null;

    const DEFAULT_EPISODE_DURATION = 24;

    public function generateRecommendation()
    {
        $this->errorMessage = null;
        $this->recommendation = null;

        if (!auth()->check()) {
            $this->errorMessage = "Devi essere collegato per usare questa funzione!";
            return;
        }

        $statuses = ['watching'];
        if ($this->includePlanToWatch) {
            $statuses[] = 'plan_to_watch';
        }

        $userTrackers = EpisodeTracker::query()
            ->where('user_id', auth()->id())
            ->whereIn('status', $statuses)
            ->get();

        if ($userTrackers->isEmpty()) {
            $this->errorMessage = "Non hai anime " . ($this->includePlanToWatch ? "in corso o da guardare" : "in corso") . " nella tua libreria!";
            return;
        }

        $metadata = app(AnimeMetadataService::class)
            ->getForMalIds($userTrackers->pluck('mal_id')->all())
            ->keyBy('mal_id');

        $shuffledTrackers = $userTrackers->shuffle();
        $found = false;

        foreach ($shuffledTrackers as $tracker) {
            $meta = $metadata->get((int) $tracker->mal_id);
            $episodeDuration = (int) ($tracker->episode_duration ?: self::DEFAULT_EPISODE_DURATION);
            $maxEpisodesPossible = (int) floor($this->availableTime / $episodeDuration);

            if ($maxEpisodesPossible >= 1) {
                $watchedEp = (int) $tracker->watched_episodes;
                $startEp = $watchedEp + 1;
                $endEp = $watchedEp + $maxEpisodesPossible;
                $totalEpisodes = $meta?->total_episodes;

                if ($totalEpisodes && $endEp > $totalEpisodes) {
                    $endEp = $totalEpisodes;
                    $maxEpisodesPossible = max(1, $endEp - $startEp + 1);
                }

                $this->recommendation = [
                    'mal_id' => (int) $tracker->mal_id,
                    'title' => $meta?->title ?? "Anime #{$tracker->mal_id}",
                    'image_url' => $meta?->image_url,
                    'total_episodes' => $totalEpisodes,
                    'episodes_to_watch' => $maxEpisodesPossible,
                    'start_episode' => $startEp,
                    'end_episode' => $endEp,
                    'total_time_needed' => $maxEpisodesPossible * $episodeDuration,
                    'status' => $tracker->status,
                ];

                $found = true;
                break;
            }
        }

        if (!$found) {
            if ($this->availableTime < 20) {
                $firstTracker = $userTrackers->first();
                $firstMeta = $firstTracker ? $metadata->get((int) $firstTracker->mal_id) : null;
                $title = $firstMeta?->title ?? ($firstTracker ? "Anime #{$firstTracker->mal_id}" : 'il tuo anime in lista');

                $this->errorMessage = "⏱️ Tempo troppo risicato per un episodio intero (serve min. 20-24 min)! " .
                    "Il contenuto che hai in lista è '{$title}' (dura ~20-24 min). Se hai un po' di margine, te lo consigliamo comunque!";
            } else {
                $this->errorMessage = "Nessun anime trovato per questo intervallo di tempo. Prova ad aumentare i minuti!";
            }
        }
    }

    public function render()
    {
        return view('livewire.anime-recommendation-modal');
    }
}
