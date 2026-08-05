<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\FavoriteAnime;
use App\Models\AnimeMetadata;
use Illuminate\Support\Facades\Auth;

class FavoriteSelector extends Component
{
    public $showModal = false;
    public $selectedSlot = 1;
    public $search = '';

    protected $listeners = ['open-favorite-modal' => 'openModal'];

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function selectSlot($slot)
    {
        $this->selectedSlot = (int)$slot;
    }

    public function assignAnimeToSlot($animeId)
    {
        $user = Auth::user();

        if (!$user || !$user->profile) {
            return;
        }

        $anime = AnimeMetadata::find($animeId);

        if (!$anime) {
            return;
        }

        FavoriteAnime::updateOrCreate(
            [
                'profile_id' => $user->profile->id,
                'slot_position' => $this->selectedSlot,
            ],
            [
                'mal_id'    => $anime->mal_id ?? $anime->id,
                'title'     => $anime->title,
                'image_url' => $anime->image_url ?? $anime->image ?? null,
            ]
        );

        $this->closeModal();
        $this->dispatch('profile-updated');
    }

    public function render()
    {
        $user = Auth::user();

        // 1. Recuperiamo tutti i mal_id già salvati nei preferiti dell'utente
        $alreadySelectedMalIds = [];
        if ($user && $user->profile) {
            $alreadySelectedMalIds = FavoriteAnime::where('profile_id', $user->profile->id)
                ->pluck('mal_id')
                ->filter()
                ->toArray();
        }

        // 2. Costruiamo la query escludendo i mal_id già usati
        $query = AnimeMetadata::query();

        if (!empty($alreadySelectedMalIds)) {
            $query->whereNotIn('mal_id', $alreadySelectedMalIds);
        }

        if (strlen($this->search) >= 2) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        $searchResults = $query->take(8)->get();

        return view('components.profile.favorite-selector', [
            'animes' => $searchResults
        ]);
    }
}
