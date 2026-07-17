<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class ShowAnime extends Component
{
    public $anime;

    public function mount($id)
    {
        try {
            // PIANO A: Proviamo a chiedere a Jikan API (MyAnimeList)
            $response = Http::timeout(3)->get("https://api.jikan.moe/v4/anime/{$id}");

            if ($response->successful()) {
                $this->anime = $response->json()['data'];
                return;
            }

            throw new \Exception("Jikan non ha trovato l'anime o è in sovraccarico.");
        } catch (\Exception $e) {
            // 🚨 PIANO B: Jikan fallisce. Interroghiamo KITSU API per non rompere la pagina!
            logger('Scheda Anime Jikan Fallita. Provo Fallback su Kitsu per ID: ' . $id);

            try {
                $kitsuResponse = Http::timeout(4)->get("https://kitsu.io/api/edge/anime/{$id}");

                if ($kitsuResponse->successful()) {
                    $item = $kitsuResponse->json()['data'] ?? null;

                    if ($item) {
                        // "Travestiamo" i dati di Kitsu adattandoli alla tua vista Blade esistente
                        $this->anime = [
                            'mal_id' => $item['id'],
                            'title' => $item['attributes']['canonicalTitle'] ?? 'Titolo Sconosciuto',
                            'synopsis' => $item['attributes']['synopsis'] ?? 'Trama non disponibile.',
                            'type' => strtoupper($item['attributes']['showType'] ?? 'TV'),
                            'episodes' => $item['attributes']['episodeCount'] ?? '?',
                            'score' => number_format(($item['attributes']['averageRating'] ?? 0) / 10, 1),
                            'images' => [
                                'jpg' => [
                                    'large_image_url' => $item['attributes']['posterImage']['large'] ?? 'https://via.placeholder.com/225x320'
                                ]
                            ]
                        ];
                        return;
                    }
                }
            } catch (\Exception $kitsuException) {
                logger('Anche Kitsu ha fallito la scheda anime: ' . $kitsuException->getMessage());
            }
        }

        // Se entrambi falliscono, lasciamo $this->anime a null così la vista mostrerà il messaggio d'errore pulito
        $this->anime = null;
    }

    public function render()
    {
        return view('components.anime.show-anime', [
            'anime' => $this->anime
        ])->layout('components.layouts.app');
    }
}
