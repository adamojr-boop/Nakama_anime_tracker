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
    public $rewatchCount = 0;
    public $episodeDuration = 24;

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
                $this->rewatchCount = $tracker->rewatch_count ?? 0;
                $this->episodeDuration = $tracker->episode_duration ?? 24;

                if (empty($this->watchedEpisodesList) && $tracker->watched_episodes > 0) {
                    $this->watchedEpisodesList = range(1, $tracker->watched_episodes);
                }
            }
        }
    }
    // 🌟 NUOVO METODO: Avvia il Rewatch della serie
    public function startRewatch()
    {
        if (!Auth::check()) return;

        $tracker = EpisodeTracker::where('user_id', Auth::id())
            ->where('mal_id', $this->malId)
            ->first();

        if ($tracker && $tracker->status === 'completed') {
            // Incrementiamo i rewatch e aggiungiamo gli episodi completati al totale storico
            $newRewatchCount = ($tracker->rewatch_count ?? 0) + 1;
            $previousRewatchedTotal = ($tracker->total_rewatched_episodes ?? 0) + count($this->watchedEpisodesList);

            // Resettiamo gli episodi per la nuova visione e impostiamo lo stato su 'watching'
            $this->watchedEpisodesList = [];
            $this->currentStatus = 'watching';
            $this->rewatchCount = $newRewatchCount;
            $this->episodeDuration = $tracker->episode_duration ?? 24;

            $tracker->update([
                'watched_episodes' => 0,
                'watched_details' => [],
                'status' => 'watching',
                'rewatch_count' => $newRewatchCount,
                'total_rewatched_episodes' => $previousRewatchedTotal,
                'episode_duration' => $this->episodeDuration,
            ]);

            $this->dispatch('listUpdated');
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
