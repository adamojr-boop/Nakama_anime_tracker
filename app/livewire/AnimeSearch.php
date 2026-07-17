<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class AnimeSearch extends Component
{
    public $searchQuery = '';
    public $results = [];

    // Questo metodo si attiva automaticamente ogni volta che l'utente scrive qualcosa
    public function updatedSearchQuery()
    {
        if (strlen($this->searchQuery) < 3) {
            $this->results = [];
            return;
        }

        try {
            // Canale Principale: Proviamo con Jikan (Timeout ridotto a 3 secondi per essere più reattivi)
            $response = Http::timeout(3)->get("https://api.jikan.moe/v4/anime", [
                'q' => $this->searchQuery,
                'limit' => 5
            ]);

            if ($response->successful()) {
                $this->results = $response->json()['data'] ?? [];
                return; // Se ha successo, usciamo dal metodo felici
            }

            // Se risponde ma con un errore (es. errore 429), lanciamo un'eccezione per attivare il Piano B
            throw new \Exception("Jikan ha risposto con errore.");
        } catch (\Exception $e) {
            // 🚨 PIANO B: Jikan è offline o in timeout. Interroghiamo KITSU API!
            logger('Jikan Fallito. Attivazione Fallback su Kitsu API. Errore: ' . $e->getMessage());

            try {
                $kitsuResponse = Http::timeout(4)->get("https://kitsu.io/api/edge/anime", [
                    'filter[text]' => $this->searchQuery,
                    'page[limit]' => 5
                ]);

                if ($kitsuResponse->successful()) {
                    $kitsuData = $kitsuResponse->json()['data'] ?? [];

                    // Dobbiamo "mappare" i dati di Kitsu per farli combaciare con la struttura di Jikan usata nella vista Blade
                    $this->results = array_map(function ($item) {
                        return [
                            // Usiamo l'ID di Kitsu come temporaneo, o l'id originale se presente
                            'mal_id' => $item['id'],
                            'title' => $item['attributes']['canonicalTitle'] ?? 'Titolo Sconosciuto',
                            'type' => strtoupper($item['attributes']['showType'] ?? 'TV'),
                            'episodes' => $item['attributes']['episodeCount'] ?? '?',
                            'score' => number_format(($item['attributes']['averageRating'] ?? 0) / 10, 1), // Kitsu usa i centesimi (es. 83.4), lo portiamo in decimi (8.3)
                            'images' => [
                                'jpg' => [
                                    'small_image_url' => $item['attributes']['posterImage']['small'] ?? 'https://via.placeholder.com/40x55'
                                ]
                            ]
                        ];
                    }, $kitsuData);
                }
            } catch (\Exception $kitsuException) {
                // Se purtroppo falliscono entrambi i server
                $this->results = [];
                logger('Anche Kitsu API è offline: ' . $kitsuException->getMessage());
            }
        }
    }

    public function render()
    {
        return view('components.anime.anime-search');
    }
}
