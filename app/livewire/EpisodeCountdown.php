<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Episode;

class EpisodeCountdown extends Component
{
    public ?Episode $episode = null;
    public string $formattedAirDate = '';

    public function mount(?Episode $episode = null)
    {
        // AUTOMATISMO: Prende il primo episodio imminente
        $this->episode = $episode ?? Episode::where('status', 'scheduled')
            ->where('air_date_utc', '>=', now())
            ->orderBy('air_date_utc', 'asc')
            ->first();

        if ($this->episode && $this->episode->air_date_utc) {
            $this->formattedAirDate = $this->episode->air_date_utc->toIso8601String();
        }
    }

    public function markAsReleased()
    {
        if ($this->episode) {
            $this->episode->update(['status' => 'released']);
        }
    }

    public function render()
    {
        return view('livewire.episode-countdown');
    }
}
