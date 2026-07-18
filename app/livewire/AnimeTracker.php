<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EpisodeTracker;
use Illuminate\Support\Facades\Auth;

class AnimeTracker extends Component
{
    public $malId;
    public $totalEpisodes;
    public $watchedEpisodesList = []; // Array che conterrà i numeri degli episodi visti

    public function mount($malId, $totalEpisodes)
    {
        $this->malId = $malId;
        // Se l'anime è in corso o non ha un numero di episodi definito dall'API
        $this->totalEpisodes = is_numeric($totalEpisodes) ? (int)$totalEpisodes : 0;

        if (Auth::check()) {
            $tracker = EpisodeTracker::where('user_id', Auth::id())
                ->where('mal_id', $this->malId)
                ->first();

            if ($tracker) {
                // Carichiamo la lista degli episodi visti salvata nel DB (se vuota, mettiamo un array vuoto)
                $this->watchedEpisodesList = $tracker->watched_details ?? [];
                // Compatibilità: se l'utente aveva usato il vecchio tracker lineare, convertiamo i vecchi dati al volo
                if (empty($this->watchedEpisodesList) && $tracker->watched_episodes > 0) {
                    $this->watchedEpisodesList = range(1, $tracker->watched_episodes);
                }
            }
        }
    }

    public function toggleEpisode($episodeNumber)
    {
        if (!Auth::check()) return;

        // Se l'episodio è già presente nella lista, lo rimuoviamo (l'utente lo sta deselezionando)
        if (in_array($episodeNumber, $this->watchedEpisodesList)) {
            $this->watchedEpisodesList = array_values(array_diff($this->watchedEpisodesList, [$episodeNumber]));
        } else {
            // Altrimenti lo aggiungiamo
            $this->watchedEpisodesList[] = $episodeNumber;
            sort($this->watchedEpisodesList); // Manteniamo l'array ordinato numericamente
        }

        $this->updateDatabase();
    }

    private function updateDatabase()
    {
        $count = count($this->watchedEpisodesList);

        // Determiniamo lo stato: se ha visto tutti gli episodi (e l'anime ha un numero totale noto), è completato
        $status = ($this->totalEpisodes > 0 && $count === $this->totalEpisodes) ? 'completed' : 'watching';

        EpisodeTracker::updateOrCreate(
            ['user_id' => Auth::id(), 'mal_id' => $this->malId],
            [
                'watched_episodes' => $count, // Manteniamo aggiornato il conteggio totale (per la Dashboard!)
                'watched_details' => $this->watchedEpisodesList, // Salviamo la griglia degli episodi specifici
                'status' => $status
            ]
        );
    }

    public function render()
    {
        return view('components.anime.anime-tracker');
    }
}
