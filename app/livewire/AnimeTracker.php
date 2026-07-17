<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EpisodeTracker;
use Illuminate\Support\Facades\Auth;

class AnimeTracker extends Component
{
    public $malId;
    public $totalEpisodes;
    public $watchedEpisodes = 0;

    public function mount($malId, $totalEpisodes)
    {
        $this->malId = $malId;
        // Se l'API restituisce '?' o nullo (es. anime in corso), impostiamo un numero altissimo
        $this->totalEpisodes = is_numeric($totalEpisodes) ? (int)$totalEpisodes : 9999;
        // Se l'utente è loggato, recuperiamo il suo progresso attuale se esiste
        if (Auth::check()) {
            $tracker = EpisodeTracker::where('user_id', Auth::id())
                ->where('mal_id', $this->malId)
                ->first();

            if ($tracker) {
                $this->watchedEpisodes = $tracker->watched_episodes;
            }
        }
    }

    public function increment()
    {
        if (!Auth::check()) return;

        if ($this->watchedEpisodes < $this->totalEpisodes) {
            $this->watchedEpisodes++;
            $this->updateDatabase();
        }
    }

    public function decrement()
    {
        if (!Auth::check()) return;

        if ($this->watchedEpisodes > 0) {
            $this->watchedEpisodes--;
            $this->updateDatabase();
        }
    }

    private function updateDatabase()
    { // Determina lo status in base agli episodi visti
        $status = ($this->watchedEpisodes == $this->totalEpisodes) ? 'completed' : 'watching';
        // updateOrCreate fa tutto da solo: se la riga c'è la aggiorna, altrimenti la crea
        EpisodeTracker::updateOrCreate(
            ['user_id' => Auth::id(), 'mal_id' => $this->malId],
            ['watched_episodes' => $this->watchedEpisodes, 'status' => $status]
        );
    }

    public function render()
    {
        return view('components.anime.anime-tracker');
    }
}
