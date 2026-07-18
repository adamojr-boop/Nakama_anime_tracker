<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EpisodeTracker;
use App\Models\CustomList;
use Illuminate\Support\Facades\Auth;

class AnimeTracker extends Component
{
    
public $malId;
public $totalEpisodes;
public $watchedEpisodesList = [];
public $currentStatus = 'none';

    public function mount($malId, $totalEpisodes)
{
    $this->malId = $malId;
    $this->totalEpisodes = is_numeric($totalEpisodes) ? (int)$totalEpisodes : 0;

    if (Auth::check()) {
        $tracker = EpisodeTracker::where('user_id', Auth::id())
            ->where('mal_id', $this->malId)
            ->first();

        if ($tracker) {
            $this->watchedEpisodesList = $tracker->watched_details ?? [];
            $this->currentStatus = $tracker->status ?? 'none'; // 🌟 Imposta lo stato iniziale
            
            if (empty($this->watchedEpisodesList) && $tracker->watched_episodes > 0) {
                $this->watchedEpisodesList = range(1, $tracker->watched_episodes);
            }
        }
    }
}

    public function toggleEpisode($episodeNumber)
    {
        if (!Auth::check()) return;

        if (in_array($episodeNumber, $this->watchedEpisodesList)) {
            $this->watchedEpisodesList = array_values(array_diff($this->watchedEpisodesList, [$episodeNumber]));
        } else {
            $this->watchedEpisodesList[] = $episodeNumber;
            sort($this->watchedEpisodesList);
        }

        $this->updateDatabase();
    }
    // 🌟 Nuova funzione per forzare lo stato (es. Abbandonato o Da Guardare) manualmente
    public function changeStatus($newStatus)
    {
        if (!Auth::check()) return;

        // Assicurati che 'dropped' sia presente in questo array di validazione
        if (!in_array($newStatus, ['watching', 'plan_to_watch', 'completed', 'dropped'])) return;

        // Aggiorna o crea il record sul database impostando lo status corretto
        \App\Models\EpisodeTracker::updateOrCreate(
            ['user_id' => Auth::id(), 'mal_id' => $this->malId],
            [
                'watched_episodes' => count($this->watchedEpisodesList),
                'watched_details' => $this->watchedEpisodesList,
                'status' => $newStatus // <-- Deve salvare il valore ricevuto ('dropped')
            ]
        );

        // Esegui la pulizia dalle liste
        if ($newStatus === 'dropped') {
            $this->cleanAnimeFromAllLists();
        }

        $this->dispatch('listUpdated');
    }

    private function updateDatabase()
    {
        $count = count($this->watchedEpisodesList);

        if ($this->totalEpisodes > 0 && $count === $this->totalEpisodes) {
            $status = 'completed';
        } elseif ($count > 0) {
            $status = 'watching';
        } else {
            $status = 'plan_to_watch';
        }

        EpisodeTracker::updateOrCreate(
            ['user_id' => Auth::id(), 'mal_id' => $this->malId],
            [
                'watched_episodes' => $count,
                'watched_details' => $this->watchedEpisodesList,
                'status' => $status
            ]
        );

        if ($status === 'dropped') {
            $this->cleanAnimeFromAllLists();
        } elseif (in_array($status, ['watching', 'completed'])) {
            $this->cleanAnimeFromWishlist();
        }

        $this->dispatch('listUpdated');
    }
    // 🌟 NUOVO METODO: Rimuove l'anime da QUALSIASI lista dell'utente
    private function cleanAnimeFromAllLists()
    {
        $allLists = CustomList::where('user_id', Auth::id())->get();
        foreach ($allLists as $list) {
            if (is_array($list->anime_ids) && in_array($this->malId, $list->anime_ids)) {
                $list->anime_ids = array_values(array_diff($list->anime_ids, [$this->malId]));
                $list->save();
            }
        }
    }
    // 🌟 NUOVO METODO: Isola la pulizia della sola Wishlist
    private function cleanAnimeFromWishlist()
    {
        $wishlist = CustomList::where('user_id', Auth::id())->where('type', 'wishlist')->first();
        if ($wishlist && is_array($wishlist->anime_ids) && in_array($this->malId, $wishlist->anime_ids)) {
            $wishlist->anime_ids = array_values(array_diff($wishlist->anime_ids, [$this->malId]));
            $wishlist->save();
        }
    }

    public function render()
    {
        return view('components.anime.anime-tracker');
    }
}
j