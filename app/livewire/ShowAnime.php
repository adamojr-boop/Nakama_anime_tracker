<?php

namespace App\Livewire;

use App\Services\AnimeDetailsService;
use Livewire\Component;

class ShowAnime extends Component
{
    public $anime;

    public function mount(int $id, AnimeDetailsService $animeDetails): void
    {
        $this->anime = $animeDetails->getForMalId($id);
    }

    public function render()
    {
        return view('components.anime.show-anime', [
            'anime' => $this->anime,
        ])->layout('components.layouts.app');
    }
}
