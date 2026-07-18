<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CustomList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ManageCustomLists extends Component
{
    public $name = '';
    public $description = '';
    public $is_public = false;

    // 🌟 Nuove variabili per gestire l'espansione e la visualizzazione degli anime
    public $selectedListId = null;
    public $selectedListAnime = [];

    protected $rules = [
        'name' => 'required|string|min:3|max:50',
        'description' => 'nullable|string|max:255',
        'is_public' => 'boolean',
    ];

    public function createList()
    {
        $this->validate();

        CustomList::create([
            'user_id' => Auth::id(),
            'name' => $this->name,
            'description' => $this->description,
            'type' => 'custom',
            'is_public' => $this->is_public,
            'anime_ids' => []
        ]);

        $this->reset(['name', 'description', 'is_public']);
        session()->flash('message', 'Lista creata con successo!');
    }

    // 🌟 Nuova funzione: Cliccando sulla lista, carica gli anime in tempo reale
    public function selectList($listId)
    {
        // Se l'utente clicca sulla lista già aperta, la chiudiamo
        if ($this->selectedListId === $listId) {
            $this->reset(['selectedListId', 'selectedListAnime']);
            return;
        }

        $list = CustomList::where('user_id', Auth::id())->find($listId);
        
        if (!$list) return;

        $this->selectedListId = $listId;
        $this->selectedListAnime = [];

        $animeIds = $list->anime_ids ?? [];

        // Recuperiamo i dettagli dall'API per ogni anime nella lista
        foreach ($animeIds as $id) {
            try {
                $response = Http::timeout(2)->get("https://api.jikan.moe/v4/anime/{$id}");
                if ($response->successful()) {
                    $apiData = $response->json()['data'];
                    $this->selectedListAnime[] = [
                        'mal_id' => $id,
                        'title' => $apiData['title'],
                        'image' => $apiData['images']['jpg']['image_url'] ?? 'https://via.placeholder.com/150x210',
                    ];
                    continue;
                }
                throw new \Exception();
            } catch (\Exception $e) {
                // Fallback Kitsu
                try {
                    $kitsuResponse = Http::timeout(2)->get("https://kitsu.io/api/edge/anime/{$id}");
                    if ($kitsuResponse->successful()) {
                        $item = $kitsuResponse->json()['data'] ?? null;
                        if ($item) {
                            $this->selectedListAnime[] = [
                                'mal_id' => $id,
                                'title' => $item['attributes']['canonicalTitle'],
                                'image' => $item['attributes']['posterImage']['medium'] ?? 'https://via.placeholder.com/150x210',
                            ];
                        }
                    }
                } catch (\Exception $kitsuEx) {
                    $this->selectedListAnime[] = [
                        'mal_id' => $id,
                        'title' => "Anime #{$id} (Offline)",
                        'image' => 'https://via.placeholder.com/150x210',
                    ];
                }
            }
        }
    }

    public function togglePrivacy($listId)
    {
        $list = CustomList::where('user_id', Auth::id())->find($listId);
        if ($list && $list->type !== 'wishlist') {
            $list->update(['is_public' => !$list->is_public]);
        }
    }

    public function deleteList($listId)
    {
        $list = CustomList::where('user_id', Auth::id())->find($listId);
        if ($list && $list->type !== 'wishlist') {
            if ($this->selectedListId === $listId) {
                $this->reset(['selectedListId', 'selectedListAnime']);
            }
            $list->delete();
        }
    }

    public function render()
    {
        // Genera la Wishlist se manca, poi restituisce tutto
        CustomList::firstOrCreate(
            ['user_id' => Auth::id(), 'type' => 'wishlist'],
            ['name' => 'Wishlist', 'description' => 'I tuoi anime da recuperare o collezionare', 'is_public' => false, 'anime_ids' => []]
        );

        return view('components.lists.manage-custom-lists', [
            'lists' => CustomList::where('user_id', Auth::id())->get()
        ]);
    }
}