<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class AnimeReviews extends Component
{
    public $animeId;
    public $comment = '';
    public $rating = 5;
    // Regole di validazione per il form
    protected $rules = [
        'comment' => 'required|string|min:5|max:1000',
        'rating' => 'required|integer|min:1|max:10',
    ];

    public function mount($animeId)
    {
        $this->animeId = $animeId;
    }

    public function saveReview()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Devi essere loggato per scrivere una recensione.');
            return;
        }

        $this->validate();

        // 🌟 CORREZIONE: Cambiato 'anime_id' in 'mal_id' per combaciare con la tua colonna su SQLite
        Review::create([
            'user_id' => Auth::id(),
            'mal_id' => $this->animeId,
            'comment' => $this->comment,
            'rating' => $this->rating,
        ]);

        $this->reset('comment');

        session()->flash('message', 'Recensione pubblicata con successo!');
    }

    public function render()
    { // Recuperiamo i commenti dal database unendo la tabella users per avere il nome dell'autore
        $reviews = Review::where('mal_id', $this->animeId)
            ->with('user') // Richiede la relazione user() definita nel modello Review
            ->latest()
            ->get();

        return view('components.anime.anime-reviews', [
            'reviews' => $reviews
        ]);
    }
}
