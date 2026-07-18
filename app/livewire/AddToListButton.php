<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CustomList;
use Illuminate\Support\Facades\Auth;

class AddToListButton extends Component
{
    public $malId;
    public $newListName = ''; // Variabile per il mini-form nel dropdown
    public function toggleAnimeInList($listId)
    {
        if (!Auth::check()) return;

        $list = CustomList::where('user_id', Auth::id())->find($listId);

        if ($list) {
            $currentIds = $list->anime_ids ?? [];

            if (in_array($this->malId, $currentIds)) {
                $list->anime_ids = array_values(array_diff($currentIds, [$this->malId]));
            } else {
                $currentIds[] = $this->malId;
                $list->anime_ids = $currentIds;
            }

            $list->save();
        }
    }
    // 🌟 Nuova funzione per creare al volo una lista e inserire subito l'anime
    public function createQuickList()
    {
        if (!Auth::check() || empty(trim($this->newListName))) return;

        $newList = CustomList::create([
            'user_id' => Auth::id(),
            'name' => trim($this->newListName),
            'type' => 'custom',
            'is_public' => false, // Di base privata, potrà modificarla dalla dashboard
            'anime_ids' => [$this->malId] // Aggiungiamo subito questo anime!
        ]);

        $this->reset('newListName');
    }

    public function render()
    {
        $myLists = Auth::check()
            ? CustomList::where('user_id', Auth::id())->get()
            : [];

        return view('components.lists.add-to-list-button', [
            'myLists' => $myLists
        ]);
    }
}
